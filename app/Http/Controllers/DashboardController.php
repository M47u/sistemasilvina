<?php

namespace App\Http\Controllers;

use App\Models\Expediente;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('Profesional')) {
            return $this->dashboardProfesional();
        }

        $totalCasos      = Expediente::count();
        $casosActivos    = Expediente::where('archivado', false)->count();
        $casosArchivados = Expediente::where('archivado', true)->count();
        $ultimosCasos    = Expediente::with(['tipoExpediente', 'persona.localidad'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalCasos', 'casosActivos', 'casosArchivados', 'ultimosCasos'
        ));
    }

    private function dashboardProfesional()
    {
        $profesional = auth()->user()->profesional;

        abort_unless($profesional, 403);

        $activos = $profesional->expedientesActivos()
            ->with(['persona.localidad', 'tipoExpediente'])
            ->orderByPivot('fecha_asignacion', 'desc')
            ->get();

        $totalHistorial = $profesional->expedientes()
            ->wherePivotNotNull('fecha_fin')
            ->count();

        return view('dashboard.profesional', compact('profesional', 'activos', 'totalHistorial'));
    }
}
