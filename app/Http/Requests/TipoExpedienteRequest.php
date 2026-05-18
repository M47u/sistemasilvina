<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TipoExpedienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:tipos_expediente,nombre',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un tipo de expediente con ese nombre.',
        ];
    }
}
