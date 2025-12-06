<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;

class GuardarExamenRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Por defecto permitir y dejar control de permisos al middleware/Controller
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'opciones_por_pregunta' => 'nullable|integer|min:2|max:4',
            'respuestas_correctas' => 'nullable|integer|min:1|max:4',
            'preguntas' => 'nullable|array',
            'preguntas.*.texto' => 'nullable|string',
            'preguntas.*.opciones' => 'nullable|array',
            'preguntas.*.opciones.*.texto' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',
        ];
    }
}
