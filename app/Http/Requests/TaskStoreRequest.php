<?php

namespace App\Http\Requests;

class TaskStoreRequest extends TaskRequest
{
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

    public function messages(): array
    {
        return [
            'name.required' => __('controllers.unique_error_task'),
            'status_id.exists' => 'The selected status is invalid.',
            'assigned_to_id.exists' => 'The selected user is invalid.',
        ];
    }
}
