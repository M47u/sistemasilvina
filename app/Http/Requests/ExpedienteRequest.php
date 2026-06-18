<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'urgente'              => $this->boolean('urgente'),
            'acepta_atencion'      => $this->boolean('acepta_atencion'),
            'servicio_legal'       => $this->boolean('servicio_legal'),
            'servicio_psicologico' => $this->boolean('servicio_psicologico'),
            'servicio_social'      => $this->boolean('servicio_social'),
            'archivado'            => $this->boolean('archivado'),
        ]);
    }

    public function rules(): array
    {
        $personaExistente = $this->filled('persona_id');

        return [
            // Persona existente (paso 2a)
            'persona_id'           => 'nullable|exists:personas,id',

            // Datos del expediente
            'fecha_recepcion'      => 'nullable|date',
            'nro_expediente'       => 'nullable|string|max:50',
            'tipo_expediente_id'   => 'required|exists:tipos_expediente,id',
            'via_acceso'           => 'nullable|string|max:100',
            'urgente'              => 'boolean',
            'denunciado'           => 'nullable|string|max:255',
            'resumen'              => 'nullable|string',
            'acepta_atencion'      => 'boolean',
            'servicio_legal'       => 'boolean',
            'servicio_psicologico' => 'boolean',
            'servicio_social'      => 'boolean',
            'archivado'            => 'boolean',
            'observaciones'        => 'nullable|string',
            'fecha_devolucion'     => 'nullable|date',

            // Datos de la persona (solo requeridos si no hay persona_id)
            'nro_legajo'           => $personaExistente ? 'nullable|string|max:50'  : 'required|string|max:50',
            'apellido_nombre'      => $personaExistente ? 'nullable|string|max:255' : 'required|string|max:255',
            'dni'                  => 'nullable|string|max:20',
            'localidad_id'         => 'nullable|exists:localidades,id',
            'barrio'               => 'nullable|string|max:255',
            'telefono'             => 'nullable|string|max:50',
            'direccion'            => 'nullable|string|max:255',

            // Asignación de profesionales (opcional, solo en create)
            'profesional_legal_id'  => 'nullable|exists:profesionales,id',
            'profesional_psico_id'  => 'nullable|exists:profesionales,id',
            'profesional_social_id' => 'nullable|exists:profesionales,id',
        ];
    }
}
