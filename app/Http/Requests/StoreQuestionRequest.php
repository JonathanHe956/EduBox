<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'El texto de la pregunta es obligatorio.',
            'options.required' => 'Debe añadir al menos dos opciones.',
        ];
    }
}
