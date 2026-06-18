<?php

namespace App\Http\Controllers;

use App\Exports\CasosExport;
use App\Http\Requests\ExpedienteRequest;
use App\Models\Expediente;
use App\Models\Localidad;
use App\Models\Persona;
use App\Models\Profesional;
use App\Models\TipoExpediente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExpedienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Expediente::with(['persona.localidad', 'tipoExpediente', 'profesionalesActivos']);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->whereHas('persona', fn($pq) => $pq
                    ->where('apellido_nombre', 'like', "%$b%")
                    ->orWhere('dni', 'like', "%$b%")
                    ->orWhere('nro_legajo', 'like', "%$b%"))
                  ->orWhere('apellido_nombre', 'like', "%$b%")
                  ->orWhere('dni', 'like', "%$b%")
                  ->orWhere('nro_expediente', 'like', "%$b%");
            });
        }

        if ($request->filled('tipo_expediente_id')) {
            $query->where('tipo_expediente_id', $request->tipo_expediente_id);
        }

        if ($request->filled('localidad_id')) {
            $query->whereHas('persona', fn($pq) => $pq->where('localidad_id', $request->localidad_id));
        }

        if ($request->filled('estado') && in_array($request->estado, \App\Models\Expediente::ESTADOS)) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('urgente')) {
            $query->where('urgente', true);
        }

        if ($request->filled('sin_fecha')) {
            $query->whereNull('fecha_recepcion');
        }

        if ($request->filled('con_errores')) {
            $query->where($this->condicionPosibleError());
        }

        $expedientes  = $query->orderBy('fecha_recepcion', 'desc')->paginate(15)->withQueryString();
        $tipos        = TipoExpediente::orderBy('nombre')->get();
        $localidades  = Localidad::orderBy('nombre')->get();
        $totalErrores = Expediente::where($this->condicionPosibleError())->count();

        return view('expedientes.index', compact('expedientes', 'tipos', 'localidades', 'totalErrores'));
    }

    private function condicionPosibleError(): \Closure
    {
        $anioActual = (int) now()->format('y');

        return function ($q) use ($anioActual) {
            $q->whereRaw('YEAR(fecha_recepcion) < 100')
              ->orWhereDate('fecha_recepcion', '>', now()->toDateString())
              ->orWhereHas('persona', function ($pq) use ($anioActual) {
                  $pq->whereRaw("CAST(SUBSTRING_INDEX(nro_legajo, '/', -1) AS UNSIGNED) > ?", [$anioActual]);
              });
        };
    }

    public function create(\Illuminate\Http\Request $request)
    {
        $tipos       = TipoExpediente::orderBy('nombre')->get();
        $localidades = Localidad::orderBy('nombre')->get();

        // Paso 2a: persona existente
        if ($request->filled('persona_id')) {
            $persona = Persona::with('localidad')->findOrFail($request->persona_id);

            $profesionalesPorArea = [];
            foreach (['legal', 'psicologia', 'social'] as $area) {
                $prof = \DB::table('expediente_profesional as ep')
                    ->join('expedientes as e', 'e.id', '=', 'ep.expediente_id')
                    ->join('profesionales as p', 'p.id', '=', 'ep.profesional_id')
                    ->where('e.persona_id', $persona->id)
                    ->where('ep.area', $area)
                    ->where('p.activo', true)
                    ->orderByDesc('ep.fecha_asignacion')
                    ->select('p.apellido', 'p.nombre')
                    ->first();
                if ($prof) {
                    $profesionalesPorArea[$area] = $prof->apellido . ', ' . $prof->nombre;
                }
            }

            $profesionales = Profesional::where('activo', true)->orderBy('apellido')->get();
            return view('expedientes.create', compact('tipos', 'localidades', 'persona', 'profesionalesPorArea', 'profesionales'));
        }

        // Paso 2b: DNI no encontrado
        if ($request->filled('dni')) {
            $dni           = $request->dni;
            $anio          = now()->format('y');
            $ultimo        = Persona::whereRaw("nro_legajo LIKE '%/$anio'")
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(nro_legajo, '/', 1) AS UNSIGNED)) as ultimo")
                ->value('ultimo') ?? 0;
            $legajoSugerido = ($ultimo + 1) . '/' . $anio;
            $profesionales  = Profesional::where('activo', true)->orderBy('apellido')->get();
            return view('expedientes.create', compact('tipos', 'localidades', 'dni', 'legajoSugerido', 'profesionales'));
        }

        // Paso 1: búsqueda por DNI
        return view('expedientes.create', compact('tipos', 'localidades'));
    }

    public function buscarPersona(\Illuminate\Http\Request $request)
    {
        $request->validate(['dni' => 'required|string|max:20']);

        $persona = Persona::where('dni', $request->dni)->first();

        if ($persona) {
            return redirect()->route('expedientes.create', ['persona_id' => $persona->id]);
        }

        return redirect()->route('expedientes.create', ['dni' => $request->dni]);
    }

    public function store(ExpedienteRequest $request)
    {
        $persona = $this->resolverPersona($request);

        $expediente = Expediente::create(array_merge(
            $this->camposExpediente($request),
            ['persona_id' => $persona->id, 'creado_por' => auth()->id()]
        ));

        if ($request->filled('persona_id')) {
            $asignados = $this->autoAsignarProfesionales($expediente, $persona, $request);
            $msg = $asignados > 0
                ? "Expediente registrado. Se asignaron automáticamente $asignados profesional(es) del historial de la persona."
                : 'Expediente registrado correctamente.';
        } else {
            $this->sincronizarProfesionales($expediente, $request);
            $msg = 'Expediente registrado correctamente.';
        }

        return redirect()->route('expedientes.show', $expediente)->with('success', $msg);
    }

    public function show(Expediente $expediente)
    {
        $expediente->load([
            'persona.localidad',
            'tipoExpediente',
            'profesionales' => fn($q) => $q->withPivot(['area', 'fecha_asignacion', 'fecha_fin', 'asignado_por']),
            'creadoPor',
            'intervenciones.creadoPor',
        ]);

        $profesionales = Profesional::with('user')->where('activo', true)->orderBy('apellido')->get();

        $miAsignacionActiva = false;
        if (auth()->user()->hasRole('Profesional')) {
            $prof = auth()->user()->profesional;
            if ($prof) {
                $miAsignacionActiva = \DB::table('expediente_profesional')
                    ->where('expediente_id', $expediente->id)
                    ->where('profesional_id', $prof->id)
                    ->whereNull('fecha_fin')
                    ->exists();
            }
        }

        return view('expedientes.show', compact('expediente', 'profesionales', 'miAsignacionActiva'));
    }

    public function darSalida(Request $request, Expediente $expediente)
    {
        abort_unless(auth()->user()->hasRole('Profesional'), 403);

        $profesional = auth()->user()->profesional;
        abort_unless($profesional, 403);

        $tieneActiva = \DB::table('expediente_profesional')
            ->where('expediente_id', $expediente->id)
            ->where('profesional_id', $profesional->id)
            ->whereNull('fecha_fin')
            ->exists();

        abort_unless($tieneActiva, 403, 'No tenés una asignación activa en este expediente.');

        $request->validate(['accion' => 'required|in:derivar,archivar']);

        \DB::table('expediente_profesional')
            ->where('expediente_id', $expediente->id)
            ->where('profesional_id', $profesional->id)
            ->whereNull('fecha_fin')
            ->update(['fecha_fin' => now()]);

        if ($request->accion === 'archivar') {
            $expediente->update(['archivado' => true, 'estado' => 'archivado']);
            $msg = 'Expediente archivado correctamente.';
        } else {
            $expediente->update(['estado' => 'derivado']);
            $msg = 'Salida registrada. El expediente queda derivado para continuar en otra área.';
        }

        return redirect()->route('mis-expedientes.index')->with('success', $msg);
    }

    public function edit(Expediente $expediente)
    {
        $expediente->load(['persona', 'profesionalesActivos']);
        $tipos         = TipoExpediente::orderBy('nombre')->get();
        $localidades   = Localidad::orderBy('nombre')->get();
        $profesionales = Profesional::with('user')->where('activo', true)->orderBy('apellido')->get();

        return view('expedientes.edit', compact('expediente', 'tipos', 'localidades', 'profesionales'));
    }

    public function update(ExpedienteRequest $request, Expediente $expediente)
    {
        $persona = $this->resolverPersona($request, $expediente->persona);
        $expediente->update(array_merge(
            $this->camposExpediente($request),
            ['persona_id' => $persona->id]
        ));

        return redirect()->route('expedientes.show', $expediente)
            ->with('success', 'Expediente actualizado correctamente.');
    }

    public function destroy(Expediente $expediente)
    {
        $expediente->delete();
        return redirect()->route('expedientes.index')->with('success', 'Expediente eliminado.');
    }

    public function asignarProfesional(Request $request, Expediente $expediente)
    {
        abort_if(
            ! auth()->user()->hasAnyRole(['Coordinadora', 'Administrativo']),
            403
        );

        $request->validate([
            'profesional_id' => 'required|exists:profesionales,id',
        ]);

        $profesional = Profesional::findOrFail($request->profesional_id);

        $yaActivo = \DB::table('expediente_profesional')
            ->where('expediente_id', $expediente->id)
            ->where('profesional_id', $profesional->id)
            ->whereNull('fecha_fin')
            ->exists();

        if ($yaActivo) {
            return back()->with('error', 'Este profesional ya está asignado activamente a este expediente.');
        }

        \DB::table('expediente_profesional')
            ->where('expediente_id', $expediente->id)
            ->whereNull('fecha_fin')
            ->update(['fecha_fin' => now()]);

        $expediente->profesionales()->attach($profesional->id, [
            'area'             => $profesional->area,
            'fecha_asignacion' => now(),
            'asignado_por'     => auth()->id(),
        ]);

        if ($expediente->estado === 'derivado') {
            $expediente->update(['estado' => 'activo']);
        }

        return back()->with('success', 'Profesional asignado correctamente.');
    }

    public function pdfExpediente(Expediente $expediente)
    {
        $expediente->load(['persona.localidad', 'tipoExpediente', 'profesionalesActivos']);

        $posiblesMembrete = [
            base_path('public/img/membrete.png'),
            base_path('../public_html/img/membrete.png'),
            base_path('../public/img/membrete.png'),
        ];
        $membrete = collect($posiblesMembrete)->first(fn($p) => file_exists($p));

        $pdf    = Pdf::loadView('expedientes.pdf_expediente', compact('expediente', 'membrete'))->setPaper('a4', 'portrait');
        $nombre = 'expediente-' . str_replace('/', '-', $expediente->nro_legajo) . '.pdf';

        return $pdf->download($nombre);
    }

    public function exportExcel()
    {
        return Excel::download(new CasosExport, 'expedientes-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf()
    {
        $expedientes = Expediente::with(['persona.localidad', 'tipoExpediente'])
            ->orderBy('fecha_recepcion', 'desc')
            ->get();

        $pdf = Pdf::loadView('expedientes.pdf', compact('expedientes'))->setPaper('a4', 'landscape');
        return $pdf->download('expedientes-' . now()->format('Y-m-d') . '.pdf');
    }

    // -------------------------------------------------------------------------

    private function resolverPersona(ExpedienteRequest $request, ?Persona $existente = null): Persona
    {
        // Paso 2a: persona ya existe, solo devolver
        if ($request->filled('persona_id')) {
            return Persona::findOrFail($request->persona_id);
        }

        $datos = [
            'nro_legajo'      => $request->nro_legajo,
            'apellido_nombre' => $request->apellido_nombre,
            'localidad_id'    => $request->localidad_id,
            'barrio'          => $request->barrio,
            'telefono'        => $request->telefono,
            'direccion'       => $request->direccion,
        ];

        if ($existente) {
            $existente->update($datos);
            return $existente;
        }

        if ($request->filled('dni')) {
            return Persona::firstOrCreate(['dni' => $request->dni], $datos);
        }

        return Persona::create(array_merge($datos, ['dni' => null]));
    }

    private function camposExpediente(ExpedienteRequest $request): array
    {
        $datos = $request->only([
            'fecha_recepcion',
            'nro_expediente',
            'tipo_expediente_id',
            'via_acceso',
            'urgente',
            'denunciado',
            'resumen',
            'acepta_atencion',
            'servicio_legal',
            'servicio_psicologico',
            'servicio_social',
            'archivado',
            'observaciones',
            'fecha_devolucion',
        ]);

        $datos['estado'] = !empty($datos['archivado']) ? 'archivado' : 'activo';

        return $datos;
    }

    private function autoAsignarProfesionales(Expediente $expediente, Persona $persona, ?Request $request = null): int
    {
        $servicios = [
            'legal'      => ['activo' => $expediente->servicio_legal,      'fallback' => 'profesional_legal_id'],
            'psicologia' => ['activo' => $expediente->servicio_psicologico, 'fallback' => 'profesional_psico_id'],
            'social'     => ['activo' => $expediente->servicio_social,      'fallback' => 'profesional_social_id'],
        ];

        $asignados = 0;

        foreach ($servicios as $area => $cfg) {
            if (!$cfg['activo']) continue;

            $profesionalId = \DB::table('expediente_profesional as ep')
                ->join('expedientes as e', 'e.id', '=', 'ep.expediente_id')
                ->join('profesionales as p', 'p.id', '=', 'ep.profesional_id')
                ->where('e.persona_id', $persona->id)
                ->where('ep.area', $area)
                ->where('p.activo', true)
                ->orderByDesc('ep.fecha_asignacion')
                ->value('ep.profesional_id');

            if (!$profesionalId && $request && $request->filled($cfg['fallback'])) {
                $profesionalId = $request->input($cfg['fallback']);
            }

            if (!$profesionalId) continue;

            $expediente->profesionales()->attach($profesionalId, [
                'area'             => $area,
                'fecha_asignacion' => now(),
                'asignado_por'     => auth()->id(),
            ]);

            $asignados++;
        }

        return $asignados;
    }

    private function sincronizarProfesionales(Expediente $expediente, ExpedienteRequest $request): void
    {
        $asignaciones = [
            'legal'      => $request->profesional_legal_id,
            'psicologia' => $request->profesional_psico_id,
            'social'     => $request->profesional_social_id,
        ];

        foreach ($asignaciones as $area => $profesionalId) {
            if (!$profesionalId) continue;

            $expediente->profesionales()->attach($profesionalId, [
                'area'             => $area,
                'fecha_asignacion' => today(),
                'asignado_por'     => auth()->id(),
            ]);
        }
    }
}
