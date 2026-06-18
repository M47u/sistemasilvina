<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('usuario')?->id;

        return [
            'apellido' => 'required|string|max:100',
            'nombre'   => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => $userId
                ? ['nullable', 'confirmed', Password::min(8)]
                : ['required', 'confirmed', Password::min(8)],
            'roles'    => 'required|array|min:1',
            'roles.*'  => 'exists:roles,name',
            'area'     => [Rule::requiredIf(fn () => in_array('Profesional', $this->input('roles', []))), 'nullable', Rule::in(['legal', 'psicologia', 'social'])],
        ];
    }

    public function messages(): array
    {
        return [
            'apellido.required' => 'El apellido es obligatorio.',
            'nombre.required'   => 'El nombre es obligatorio.',
            'roles.required'    => 'Debe asignar al menos un rol.',
            'area.required_if'  => 'El área es obligatoria para profesionales.',
        ];
    }
}
