<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Intervencion;
use Illuminate\Http\Request;

class IntervencionController extends Controller
{
    public function store(Request $request, Expediente $expediente)
    {
        $request->validate([
            'tipo'        => 'required|in:entrevista,llamada,seguimiento,derivacion,nota',
            'fecha'       => 'required|date',
            'descripcion' => 'required|string|max:2000',
        ]);

        $expediente->intervenciones()->create([
            'tipo'        => $request->tipo,
            'fecha'       => $request->fecha,
            'descripcion' => $request->descripcion,
            'creado_por'  => auth()->id(),
        ]);

        return back()->with('success', 'Intervención registrada.');
    }

    public function destroy(Expediente $expediente, Intervencion $intervencion)
    {
        abort_if(! auth()->user()->hasRole('Coordinadora'), 403);

        $intervencion->delete();

        return back()->with('success', 'Intervención eliminada.');
    }
}
