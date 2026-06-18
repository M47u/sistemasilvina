<?php

namespace App\Http\Controllers;

class MisExpedientesController extends Controller
{
    public function index()
    {
        $profesional = auth()->user()->profesional;

        abort_unless($profesional, 403);

        $activos = $profesional->expedientesActivos()
            ->with(['persona.localidad', 'tipoExpediente'])
            ->orderByPivot('fecha_asignacion', 'desc')
            ->get();

        $historial = $profesional->expedientes()
            ->wherePivotNotNull('fecha_fin')
            ->with(['persona', 'tipoExpediente'])
            ->orderByPivot('fecha_asignacion', 'desc')
            ->get();

        return view('mis_expedientes.index', compact('profesional', 'activos', 'historial'));
    }
}
