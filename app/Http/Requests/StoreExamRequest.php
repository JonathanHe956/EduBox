<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Por defecto permitir y dejar control de permisos al middleware/Controller
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options_per_question' => 'nullable|integer|min:2|max:8',
            'correct_answers' => 'nullable|integer|min:1|max:8',
            'questions' => 'nullable|array',
            'questions.*.text' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.text' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede superar los 255 caracteres.',
        ];
    }
}
