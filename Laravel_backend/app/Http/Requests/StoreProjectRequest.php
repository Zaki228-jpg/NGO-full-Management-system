<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'sector' => ['required', 'in:education,health,wash,livelihood,protection,other'],
            'province' => ['required', 'string', 'max:100'],
            'budget' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:planned,ongoing,completed,suspended'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
