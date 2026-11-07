<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PacienteRequest extends FormRequest
{
    /** Los errores van en su propia "bolsa" para reabrir el modal del paciente al fallar. */
    protected $errorBag = 'paciente';

    public function authorize(): bool
    {
        return true; // el acceso ya lo controla el middleware "auth"
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'sexo' => ['required', 'in:F,M'],
            'estatura_m' => ['required', 'numeric', 'between:0.5,2.5'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:150'],
            'antecedentes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Escribe el nombre.',
            'nombre.max' => 'El nombre admite hasta 80 caracteres.',
            'apellidos.required' => 'Escribe los apellidos.',
            'apellidos.max' => 'Los apellidos admiten hasta 100 caracteres.',
            'fecha_nacimiento.required' => 'Indica una fecha de nacimiento válida.',
            'fecha_nacimiento.date' => 'Indica una fecha de nacimiento válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'sexo.required' => 'Selecciona una opción.',
            'sexo.in' => 'Selecciona una opción.',
            'estatura_m.required' => 'La estatura debe estar entre 0.50 y 2.50 m.',
            'estatura_m.numeric' => 'La estatura debe estar entre 0.50 y 2.50 m.',
            'estatura_m.between' => 'La estatura debe estar entre 0.50 y 2.50 m.',
            'telefono.max' => 'El teléfono admite hasta 20 caracteres.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.max' => 'El correo admite hasta 150 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->input('nombre')),
            'apellidos' => trim((string) $this->input('apellidos')),
        ]);
    }
}
