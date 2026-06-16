<?php

namespace App\Console\Commands;

use App\Models\Caso;
use App\Models\Localidad;
use App\Models\Persona;
use App\Models\TipoExpediente;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class MigrarExcel extends Command
{
    protected $signature = 'migrar:excel
                            {archivo? : Ruta al archivo .xlsx (default: LEGAJOS 2025-2026.xlsx en raíz del proyecto)}
                            {--dry-run : Simula la migración sin guardar datos}';

    protected $description = 'Importa datos del Excel LEGAJOS 2025-2026 a la base de datos';

    private array $errores   = [];
    private array $warnings  = [];
    private array $profesionalesPendientes = [];

    private array $mapaTipos = [
        'OFICIO'          => 'Oficio',
        'OFICIO J'        => 'Oficio Judicial',
        'OFICIO JU'       => 'Oficio Judicial',
        'OFICIO V'        => 'Oficio de Violencia',
        'PPP'             => 'PPP',
        'NOTA'            => 'Nota',
        'MEMORANDUM'      => 'Memorandum',
        'RADIOGRAMA'      => 'Radiograma',
        'TEXTO'           => 'Texto',
        'EXPEDIENTE'      => 'Expediente',
        'NOTA MCOMUNIDAD' => 'Nota Ministerio Comunidad',
        'UAVT'            => 'UAVT',
        'UAVT NOTA'       => 'UAVT Nota',
    ];

    private array $mapaVia = [
        'DESPACHO'        => 'Despacho',
        'PRESENCIAL'      => 'Presencial',
        'TELEFONICO'      => 'Telefónico',
        'GUARDIA'         => 'Guardia',
        'VISITA'          => 'Visita',
        'REDES SOCIALES'  => 'Redes sociales',
        'NUEVA FORMOSA'   => 'Nueva Formosa',
        'PEDIDO LIC'      => 'Pedido licencia',
        'HOSP-MADREYNIÑO' => 'Hospital Madre y Niño',
        'SEGUIMIENTO'     => 'Seguimiento',
    ];

    public function handle(): int
    {
        $archivo  = $this->argument('archivo') ?? base_path('LEGAJOS 2025-2026.xlsx');
        $dryRun   = $this->option('dry-run');

        if (!file_exists($archivo)) {
            $this->error("Archivo no encontrado: $archivo");
            return 1;
        }

        $modo = $dryRun ? ' [DRY RUN — sin guardar]' : '';
        $this->info("Cargando Excel$modo...");

        $spreadsheet = IOFactory::load($archivo);
        $sheet       = $spreadsheet->getActiveSheet();
        $maxRow      = $sheet->getHighestRow();
        $maxCol      = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $this->info("Total filas: $maxRow");

        // Pre-cargar tipos existentes para no hacer queries en el loop
        $tipos = TipoExpediente::pluck('id', 'nombre')->toArray();

        $stats = [
            'personas_creadas'   => 0,
            'personas_existentes'=> 0,
            'casos_creados'      => 0,
            'stubs_creados'      => 0,
            'filas_saltadas'     => 0,
        ];

        $bar = $this->output->createProgressBar($maxRow);
        $bar->start();

        for ($row = 2; $row <= $maxRow; $row++) {
            $bar->advance();

            $legajo = trim($sheet->getCell("C$row")->getFormattedValue());

            // Saltar filas de agrupación/mes (sin legajo)
            if ($legajo === '') {
                $stats['filas_saltadas']++;
                continue;
            }

            $nombre = trim($sheet->getCell("G$row")->getFormattedValue());

            // Nombre vacío → error, no migrar
            if (strlen($nombre) < 3) {
                $this->errores[] = "Fila $row (legajo $legajo): nombre vacío o inválido — omitida";
                $stats['filas_saltadas']++;
                continue;
            }

            // Legajo duplicado → warning pero migrar igual
            if (!$dryRun && Caso::where('nro_legajo', $legajo)->exists()) {
                $this->warnings[] = "Fila $row: legajo $legajo ya existe en la base de datos — omitida";
                $stats['filas_saltadas']++;
                continue;
            }

            try {
                DB::beginTransaction();

                // ── Persona ──────────────────────────────────────────────
                $dniRaw    = trim($sheet->getCell("H$row")->getFormattedValue());
                $dni       = $this->limpiarDni($dniRaw);
                $localidad = $this->resolverLocalidad(
                    trim($sheet->getCell("I$row")->getFormattedValue()),
                    $dryRun
                );

                $datosPersona = [
                    'apellido_nombre' => $nombre,
                    'localidad_id'    => $localidad?->id,
                ];

                if (!$dryRun) {
                    if ($dni && $this->dniValido($dniRaw)) {
                        [$persona, $nueva] = $this->findOrCreatePersona($dni, $datosPersona);
                    } else {
                        // DNI inválido (S/D, S/N, etc.) → siempre crear persona nueva
                        $persona = Persona::create(array_merge($datosPersona, ['dni' => null]));
                        $nueva   = true;
                        if ($dniRaw !== '') {
                            $this->warnings[] = "Fila $row (legajo $legajo): DNI inválido \"$dniRaw\" — persona creada sin DNI";
                        }
                    }

                    $nueva ? $stats['personas_creadas']++ : $stats['personas_existentes']++;
                }

                // ── Tipo expediente ───────────────────────────────────────
                $tipoRaw = strtoupper(trim($sheet->getCell("E$row")->getFormattedValue()));
                $tipoNombre = $this->mapaTipos[$tipoRaw] ?? ($tipoRaw ?: 'Sin clasificar');

                if (!$dryRun) {
                    if (!isset($tipos[$tipoNombre])) {
                        $tipo = TipoExpediente::firstOrCreate(['nombre' => $tipoNombre]);
                        $tipos[$tipoNombre] = $tipo->id;
                    }
                    $tipoId = $tipos[$tipoNombre];
                }

                // ── Vía de acceso ─────────────────────────────────────────
                $viaRaw = strtoupper(trim($sheet->getCell("D$row")->getFormattedValue()));
                $via    = $this->mapaVia[$viaRaw] ?? ($viaRaw ?: null);
                // Si via_acceso estaba en columna F (casos donde D vacío y F tiene texto no-expediente)
                if (!$via) {
                    $fRaw = trim($sheet->getCell("F$row")->getFormattedValue());
                    if ($fRaw && !preg_match('/^[A-Za-z]\s*[\-\s]?\d{4,}/', $fRaw)) {
                        $via = $fRaw;
                    }
                }

                // ── Número expediente ─────────────────────────────────────
                $nroExp = trim($sheet->getCell("F$row")->getFormattedValue());
                // Si el valor de F parece una vía de acceso, no es expediente
                if ($nroExp && !preg_match('/^[A-Za-z]\s*[\-\s]?\d{3,}/', $nroExp)) {
                    $nroExp = null;
                }

                // ── Fecha ─────────────────────────────────────────────────
                $fecha = $this->parsearFecha($sheet->getCell("A$row")->getFormattedValue(), $row, $legajo);

                // ── Urgente ───────────────────────────────────────────────
                $bRaw   = strtoupper(trim($sheet->getCell("B$row")->getFormattedValue()));
                $urgente = str_contains($bRaw, 'URGENTE');

                // ── Crear caso ────────────────────────────────────────────
                if (!$dryRun) {
                    $caso = Caso::create([
                        'persona_id'           => $persona->id,
                        'fecha_recepcion'      => $fecha,
                        'nro_legajo'           => $legajo,
                        'nro_expediente'       => $nroExp ?: null,
                        'tipo_expediente_id'   => $tipoId,
                        'via_acceso'           => $via,
                        'urgente'              => $urgente,
                        'acepta_atencion'      => false,
                        'servicio_legal'       => !empty(trim($sheet->getCell("K$row")->getFormattedValue())),
                        'servicio_psicologico' => !empty(trim($sheet->getCell("L$row")->getFormattedValue())),
                        'servicio_social'      => !empty(trim($sheet->getCell("M$row")->getFormattedValue())),
                        'archivado'            => false,
                    ]);
                    $stats['casos_creados']++;

                    // ── Profesionales pendientes (para reporte) ───────────
                    $this->registrarProfesionalPendiente($caso->id, $legajo, 'legal',
                        trim($sheet->getCell("K$row")->getFormattedValue()));
                    $this->registrarProfesionalPendiente($caso->id, $legajo, 'psicologia',
                        trim($sheet->getCell("L$row")->getFormattedValue()));
                    $this->registrarProfesionalPendiente($caso->id, $legajo, 'social',
                        trim($sheet->getCell("M$row")->getFormattedValue()));

                    // ── Casos vinculados (columnas N en adelante) ─────────
                    $stubs = $this->extraerStubs($sheet, $row, $maxCol);
                    foreach ($stubs as $refExp) {
                        Caso::create([
                            'persona_id'         => $persona->id,
                            'fecha_recepcion'    => now()->toDateString(),
                            'nro_legajo'         => 'STUB-' . $caso->id . '-' . uniqid(),
                            'nro_expediente'     => $refExp,
                            'tipo_expediente_id' => $tipoId,
                            'urgente'            => false,
                            'archivado'          => false,
                        ]);
                        $stats['stubs_creados']++;
                    }
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->errores[] = "Fila $row (legajo $legajo): " . $e->getMessage();
            }
        }

        $bar->finish();
        $this->newLine(2);

        // ── Reporte ───────────────────────────────────────────────────────
        $this->mostrarResumen($stats, $dryRun);
        $this->generarReporte($stats, $dryRun);

        return 0;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function limpiarDni(string $dni): string
    {
        return str_replace(['.', ' ', '-'], '', $dni);
    }

    private function dniValido(string $dniRaw): bool
    {
        $limpio = $this->limpiarDni($dniRaw);
        return preg_match('/^\d{7,8}$/', $limpio) === 1;
    }

    private function findOrCreatePersona(string $dni, array $datos): array
    {
        $existing = Persona::where('dni', $dni)->first();

        if ($existing) {
            return [$existing, false];
        }

        return [Persona::create(array_merge($datos, ['dni' => $dni])), true];
    }

    private function resolverLocalidad(string $nombre, bool $dryRun): ?Localidad
    {
        if ($nombre === '') return null;

        if ($dryRun) return null;

        return Localidad::firstOrCreate(['nombre' => ucwords(strtolower($nombre))]);
    }

    private function parsearFecha(string $valor, int $row, string $legajo): ?string
    {
        $valor = trim($valor);

        if ($valor === '') return null;

        // Número serial de Excel
        if (is_numeric($valor) && strlen($valor) === 5) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$valor)
                    ->format('Y-m-d');
            } catch (\Throwable) {}
        }

        // Formatos textuales
        $formatos = ['d/m/Y', 'd/m/y', 'd-m-Y', 'd-m-y', 'Y-m-d', 'd/m'];
        foreach ($formatos as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $valor);
                if ($dt) return $dt->format('Y-m-d');
            } catch (\Throwable) {}
        }

        $this->warnings[] = "Fila $row (legajo $legajo): fecha inválida \"$valor\" — pendiente asignación";
        return null;
    }

    private function extraerStubs(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $row, int $maxCol): array
    {
        $stubs = [];
        // Columnas N (14) en adelante
        for ($col = 14; $col <= $maxCol; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $valor     = trim($sheet->getCell("$colLetter$row")->getFormattedValue());

            if ($valor === '' || strlen($valor) < 3) continue;

            // Ignorar valores que son solo puntuación (typos)
            if (preg_match('/^[.\\/\-\s]+$/', $valor)) continue;

            $stubs[] = $valor;
        }

        return $stubs;
    }

    private function registrarProfesionalPendiente(int $casoId, string $legajo, string $area, string $valor): void
    {
        if ($valor === '') return;

        // Solo registrar si parece un apellido simple (sin saltos de línea ni notas)
        $lineas = array_filter(array_map('trim', explode("\n", $valor)));
        $nombre = count($lineas) === 1 ? $lineas[0] : implode(' / ', $lineas);

        if ($nombre) {
            $this->profesionalesPendientes[] = [
                'caso_id' => $casoId,
                'legajo'  => $legajo,
                'area'    => $area,
                'nombre'  => $nombre,
            ];
        }
    }

    private function mostrarResumen(array $stats, bool $dryRun): void
    {
        $this->table(
            ['Concepto', 'Total'],
            [
                ['Personas creadas',    $stats['personas_creadas']],
                ['Personas existentes', $stats['personas_existentes']],
                ['Casos creados',       $stats['casos_creados']],
                ['Casos vinculados (stub)', $stats['stubs_creados']],
                ['Filas saltadas',      $stats['filas_saltadas']],
                ['Errores',             count($this->errores)],
                ['Warnings',            count($this->warnings)],
            ]
        );

        if ($dryRun) {
            $this->warn('DRY RUN: no se guardó nada.');
        }
    }

    private function generarReporte(array $stats, bool $dryRun): void
    {
        $lineas   = [];
        $lineas[] = '=== REPORTE DE MIGRACIÓN — ' . now()->format('d/m/Y H:i') . ($dryRun ? ' [DRY RUN]' : '') . ' ===';
        $lineas[] = '';
        $lineas[] = 'ESTADÍSTICAS';
        $lineas[] = str_repeat('-', 40);
        foreach ($stats as $k => $v) {
            $lineas[] = "$k: $v";
        }

        if ($this->errores) {
            $lineas[] = '';
            $lineas[] = 'ERRORES (' . count($this->errores) . ')';
            $lineas[] = str_repeat('-', 40);
            foreach ($this->errores as $e) {
                $lineas[] = "  ✗ $e";
            }
        }

        if ($this->warnings) {
            $lineas[] = '';
            $lineas[] = 'WARNINGS (' . count($this->warnings) . ')';
            $lineas[] = str_repeat('-', 40);
            foreach ($this->warnings as $w) {
                $lineas[] = "  ⚠ $w";
            }
        }

        if ($this->profesionalesPendientes) {
            $lineas[] = '';
            $lineas[] = 'ASIGNACIONES DE PROFESIONALES PENDIENTES (' . count($this->profesionalesPendientes) . ')';
            $lineas[] = 'Crear las cuentas de usuario para estos profesionales y asignarlos manualmente.';
            $lineas[] = str_repeat('-', 40);

            // Agrupar por área y nombre para facilitar la tarea
            $porArea = [];
            foreach ($this->profesionalesPendientes as $p) {
                $porArea[$p['area']][$p['nombre']][] = $p['legajo'];
            }

            foreach ($porArea as $area => $profesionales) {
                $lineas[] = '';
                $lineas[] = strtoupper($area) . ':';
                foreach ($profesionales as $nombre => $legajos) {
                    $lineas[] = "  $nombre → " . count($legajos) . " casos (ej: " . implode(', ', array_slice($legajos, 0, 3)) . ')';
                }
            }
        }

        $ruta = storage_path('logs/migracion-excel-' . now()->format('Y-m-d-His') . '.txt');
        file_put_contents($ruta, implode(PHP_EOL, $lineas));

        $this->info("Reporte guardado en: $ruta");

        if (count($this->errores) > 0) {
            $this->error(count($this->errores) . ' errores encontrados. Revisar el reporte.');
        }
    }
}
