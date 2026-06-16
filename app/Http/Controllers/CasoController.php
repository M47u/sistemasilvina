<?php

namespace App\Http\Controllers;

use App\Exports\CasosExport;
use App\Http\Requests\CasoRequest;
use App\Models\Caso;
use App\Models\Localidad;
use App\Models\Persona;
use App\Models\Profesional;
use App\Models\TipoExpediente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CasoController extends Controller
{
    public function index(Request $request)
    {
        $query = Caso::with(['persona.localidad', 'tipoExpediente', 'profesionalesActivos']);

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->whereHas('persona', fn($pq) => $pq
                    ->where('apellido_nombre', 'like', "%$b%")
                    ->orWhere('dni', 'like', "%$b%"))
                  ->orWhere('apellido_nombre', 'like', "%$b%")
                  ->orWhere('dni', 'like', "%$b%")
                  ->orWhere('nro_legajo', 'like', "%$b%")
                  ->orWhere('nro_expediente', 'like', "%$b%");
            });
        }

        if ($request->filled('tipo_expediente_id')) {
            $query->where('tipo_expediente_id', $request->tipo_expediente_id);
        }

        if ($request->filled('localidad_id')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('persona', fn($pq) => $pq->where('localidad_id', $request->localidad_id))
                  ->orWhere('localidad_id', $request->localidad_id);
            });
        }

        if ($request->filled('archivado') && $request->archivado !== '') {
            $query->where('archivado', $request->archivado);
        }

        if ($request->filled('urgente')) {
            $query->where('urgente', true);
        }

        if ($request->filled('sin_fecha')) {
            $query->whereNull('fecha_recepcion');
        }

        $casos       = $query->orderBy('fecha_recepcion', 'desc')->paginate(15)->withQueryString();
        $tipos       = TipoExpediente::orderBy('nombre')->get();
        $localidades = Localidad::orderBy('nombre')->get();

        return view('casos.index', compact('casos', 'tipos', 'localidades'));
    }

    public function create()
    {
        $tipos         = TipoExpediente::orderBy('nombre')->get();
        $localidades   = Localidad::orderBy('nombre')->get();
        $profesionales = Profesional::with('user')->where('activo', true)->orderBy('apellido')->get();

        return view('casos.create', compact('tipos', 'localidades', 'profesionales'));
    }

    public function store(CasoRequest $request)
    {
        $persona = $this->resolverPersona($request);

        $caso = Caso::create(array_merge(
            $this->camposCaso($request),
            ['persona_id' => $persona->id, 'creado_por' => auth()->id()]
        ));

        $this->sincronizarProfesionales($caso, $request);

        return redirect()->route('casos.show', $caso)->with('success', 'Caso registrado correctamente.');
    }

    public function show(Caso $caso)
    {
        $caso->load([
            'persona.localidad',
            'tipoExpediente',
            'profesionales' => fn($q) => $q->withPivot(['area', 'fecha_asignacion', 'fecha_fin', 'asignado_por']),
            'creadoPor',
        ]);

        $profesionales = Profesional::with('user')->where('activo', true)->orderBy('apellido')->get();

        return view('casos.show', compact('caso', 'profesionales'));
    }

    public function edit(Caso $caso)
    {
        $caso->load(['persona', 'profesionalesActivos']);
        $tipos         = TipoExpediente::orderBy('nombre')->get();
        $localidades   = Localidad::orderBy('nombre')->get();
        $profesionales = Profesional::with('user')->where('activo', true)->orderBy('apellido')->get();

        return view('casos.edit', compact('caso', 'tipos', 'localidades', 'profesionales'));
    }

    public function update(CasoRequest $request, Caso $caso)
    {
        $persona = $this->resolverPersona($request, $caso->persona);
        $caso->update(array_merge(
            $this->camposCaso($request),
            ['persona_id' => $persona->id]
        ));

        return redirect()->route('casos.show', $caso)->with('success', 'Caso actualizado correctamente.');
    }

    public function destroy(Caso $caso)
    {
        $caso->delete();
        return redirect()->route('casos.index')->with('success', 'Caso eliminado.');
    }

    public function asignarProfesional(Request $request, Caso $caso)
    {
        $request->validate([
            'profesional_id' => 'required|exists:profesionales,id',
            'area'           => 'required|in:legal,psicologia,social',
        ]);

        // Cerrar asignación activa del mismo área si existe
        $caso->profesionales()
            ->wherePivot('area', $request->area)
            ->wherePivotNull('fecha_fin')
            ->each(fn($p) => $caso->profesionales()->updateExistingPivot($p->id, [
                'fecha_fin' => today(),
            ]));

        $caso->profesionales()->attach($request->profesional_id, [
            'area'             => $request->area,
            'fecha_asignacion' => today(),
            'asignado_por'     => auth()->id(),
        ]);

        return back()->with('success', 'Profesional asignado correctamente.');
    }

    public function pdfCaso(Caso $caso)
    {
        $caso->load(['persona.localidad', 'tipoExpediente', 'profesionalesActivos']);

        $posiblesMembrete = [
            base_path('public/img/membrete.png'),
            base_path('../public_html/img/membrete.png'),
            base_path('../public/img/membrete.png'),
        ];
        $membrete = collect($posiblesMembrete)->first(fn($p) => file_exists($p));

        $pdf    = Pdf::loadView('casos.pdf_caso', compact('caso', 'membrete'))->setPaper('a4', 'portrait');
        $nombre = 'caso-' . str_replace('/', '-', $caso->nro_legajo) . '.pdf';

        return $pdf->download($nombre);
    }

    public function exportExcel()
    {
        return Excel::download(new CasosExport, 'casos-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf()
    {
        $casos = Caso::with(['persona.localidad', 'tipoExpediente'])
            ->orderBy('fecha_recepcion', 'desc')
            ->get();

        $pdf = Pdf::loadView('casos.pdf', compact('casos'))->setPaper('a4', 'landscape');
        return $pdf->download('casos-' . now()->format('Y-m-d') . '.pdf');
    }

    // -------------------------------------------------------------------------

    private function resolverPersona(CasoRequest $request, ?Persona $existente = null): Persona
    {
        $datos = [
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

    private function camposCaso(CasoRequest $request): array
    {
        return $request->only([
            'fecha_recepcion',
            'nro_legajo',
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
    }

    private function sincronizarProfesionales(Caso $caso, CasoRequest $request): void
    {
        $asignaciones = [
            'legal'      => $request->profesional_legal_id,
            'psicologia' => $request->profesional_psico_id,
            'social'     => $request->profesional_social_id,
        ];

        foreach ($asignaciones as $area => $profesionalId) {
            if (!$profesionalId) continue;

            $caso->profesionales()->attach($profesionalId, [
                'area'             => $area,
                'fecha_asignacion' => today(),
                'asignado_por'     => auth()->id(),
            ]);
        }
    }
}
