<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'plan' => ['nullable', 'string', 'max:10000'],

            // Mediciones (los límites coinciden con los CHECK de MySQL)
            'peso' => ['required', 'numeric', 'between:0.1,400'],
            'porcentaje_grasa' => ['nullable', 'numeric', 'between:0,75'],
            'agua_corporal' => ['nullable', 'numeric', 'between:0,100'],
            'masa_muscular' => ['nullable', 'numeric', 'between:0.1,200'],
            'masa_osea' => ['nullable', 'numeric', 'between:0.1,20'],
            'grasa_visceral' => ['nullable', 'integer', 'between:1,59'],
            'tasa_metabolica_basal' => ['nullable', 'integer', 'between:500,6000'],
            'edad_metabolica' => ['nullable', 'integer', 'between:5,120'],
            'valoracion_fisica' => ['nullable', 'integer', 'between:1,9'],

            'medicamentos' => ['nullable', 'array', 'max:30'],
            'medicamentos.*.nombre' => ['nullable', 'string', 'max:120'],
            'medicamentos.*.dosis' => ['nullable', 'string', 'max:80'],
            'medicamentos.*.frecuencia' => ['nullable', 'string', 'max:80'],

            // La cita no puede quedar en el pasado (MySQL no permite NOW() en un CHECK)
            'cita_fecha' => ['nullable', 'date', 'after:now'],
            'cita_motivo' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'Indica la fecha de la consulta.',
            'fecha.date' => 'Indica la fecha de la consulta.',
            'peso.required' => 'El peso es obligatorio y debe estar entre 0.1 y 400 kg.',
            'peso.numeric' => 'El peso es obligatorio y debe estar entre 0.1 y 400 kg.',
            'peso.between' => 'El peso es obligatorio y debe estar entre 0.1 y 400 kg.',
            'porcentaje_grasa.*' => 'Entre 0 y 75 %.',
            'agua_corporal.*' => 'Entre 0 y 100 %.',
            'masa_muscular.*' => 'Debe ser mayor que 0 (máximo 200 kg).',
            'masa_osea.*' => 'Debe ser mayor que 0 (máximo 20 kg).',
            'grasa_visceral.*' => 'Nivel entero entre 1 y 59.',
            'tasa_metabolica_basal.*' => 'Entero entre 500 y 6000 kcal.',
            'edad_metabolica.*' => 'Entero entre 5 y 120 años.',
            'valoracion_fisica.*' => 'Escala entera de 1 a 9.',
            'cita_fecha.date' => 'Indica una fecha válida para la próxima cita.',
            'cita_fecha.after' => 'La próxima cita debe ser una fecha futura.',
        ];
    }
}
