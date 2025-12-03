<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarPreguntaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'texto' => 'required|string',
            'opciones' => 'required|array|min:2',
            'opciones.*.texto' => 'required|string',
            'opciones.*.es_correcta' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'texto.required' => 'El texto de la pregunta es obligatorio.',
            'opciones.required' => 'Debe añadir al menos dos opciones.',
        ];
    }
}
