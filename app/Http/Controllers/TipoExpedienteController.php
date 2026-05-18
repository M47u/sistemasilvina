<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoExpedienteRequest;
use App\Models\TipoExpediente;

class TipoExpedienteController extends Controller
{
    public function index()
    {
        $tipos = TipoExpediente::orderBy('nombre')->paginate(15);
        return view('tipos_expediente.index', compact('tipos'));
    }

    public function store(TipoExpedienteRequest $request)
    {
        TipoExpediente::create($request->validated());
        return redirect()->route('tipos-expediente.index')->with('success', 'Tipo de expediente agregado.');
    }

    public function destroy(TipoExpediente $tipoExpediente)
    {
        if ($tipoExpediente->casos()->exists()) {
            return redirect()->route('tipos-expediente.index')
                ->with('error', 'No se puede eliminar: tiene casos asociados.');
        }
        $tipoExpediente->delete();
        return redirect()->route('tipos-expediente.index')->with('success', 'Tipo eliminado.');
    }
}
