<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CasoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'acepta_atencion'      => $this->boolean('acepta_atencion'),
            'servicio_legal'       => $this->boolean('servicio_legal'),
            'servicio_psicologico' => $this->boolean('servicio_psicologico'),
            'servicio_social'      => $this->boolean('servicio_social'),
            'archivado'            => $this->boolean('archivado'),
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha_recepcion'      => 'required|date',
            'nro_legajo'           => 'required|string|max:50',
            'nro_expediente'       => 'required|string|max:50',
            'tipo_expediente_id'   => 'required|exists:tipos_expediente,id',
            'apellido_nombre'      => 'required|string|max:255',
            'dni'                  => 'required|string|max:20',
            'localidad_id'         => 'required|exists:localidades,id',
            'barrio'               => 'nullable|string|max:255',
            'telefono'             => 'nullable|string|max:50',
            'denunciado'           => 'nullable|string|max:255',
            'resumen'              => 'nullable|string',
            'acepta_atencion'      => 'boolean',
            'servicio_legal'       => 'boolean',
            'servicio_psicologico' => 'boolean',
            'servicio_social'      => 'boolean',
            'archivado'            => 'boolean',
            'observaciones'        => 'nullable|string',
            'fecha_devolucion'     => 'nullable|date',
        ];
    }
}
