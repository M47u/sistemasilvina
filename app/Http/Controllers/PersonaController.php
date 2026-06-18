<?php

namespace App\Http\Controllers;

use App\Models\Persona;

class PersonaController extends Controller
{
    public function expedientes(Persona $persona)
    {
        $expedientes = $persona->expedientes()
            ->with(['tipoExpediente'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('personas.expedientes', compact('persona', 'expedientes'));
    }
}
