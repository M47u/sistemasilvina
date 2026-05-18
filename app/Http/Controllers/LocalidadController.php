<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocalidadRequest;
use App\Models\Localidad;

class LocalidadController extends Controller
{
    public function index()
    {
        $localidades = Localidad::orderBy('nombre')->paginate(15);
        return view('localidades.index', compact('localidades'));
    }

    public function store(LocalidadRequest $request)
    {
        Localidad::create($request->validated());
        return redirect()->route('localidades.index')->with('success', 'Localidad agregada.');
    }

    public function destroy(Localidad $localidad)
    {
        if ($localidad->casos()->exists()) {
            return redirect()->route('localidades.index')
                ->with('error', 'No se puede eliminar: tiene casos asociados.');
        }
        $localidad->delete();
        return redirect()->route('localidades.index')->with('success', 'Localidad eliminada.');
    }
}
