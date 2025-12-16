<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:task_statuses,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('assigned_to_id') && $this->input('assigned_to_id') === '') {
            $this->merge([
                'assigned_to_id' => null,
            ]);
        }

        if ($this->has('labels')) {
            $filteredLabels = array_filter($this->input('labels'), fn($label) => $label !== null);
            $this->merge([
                'labels' => $filteredLabels,
            ]);
        }
    }
}
