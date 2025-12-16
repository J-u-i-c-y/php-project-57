<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('controllers.unique_error_task'),
            'status_id.exists' => 'The selected status is invalid.',
            'assigned_to_id.exists' => 'The selected user is invalid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
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
