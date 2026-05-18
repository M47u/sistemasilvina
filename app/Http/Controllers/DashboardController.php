<?php

namespace App\Http\Controllers;

use App\Models\Caso;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCasos      = Caso::count();
        $casosActivos    = Caso::where('archivado', false)->count();
        $casosArchivados = Caso::where('archivado', true)->count();
        $ultimosCasos    = Caso::with(['tipoExpediente', 'localidad'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalCasos', 'casosActivos', 'casosArchivados', 'ultimosCasos'
        ));
    }
}
