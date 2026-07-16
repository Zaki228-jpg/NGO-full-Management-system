<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('project')) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'sector' => ['sometimes', 'in:education,health,wash,livelihood,protection,other'],
            'province' => ['sometimes', 'string', 'max:100'],
            'budget' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:planned,ongoing,completed,suspended'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
