<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $taskStatusId = $this->route('task_status')->id;

        return [
            'name' => "required|unique:task_statuses,name,{$taskStatusId}|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.unique' => __('controllers.unique_error_status'),
            'name.max' => __('validation.max.string'),
        ];
    }
}
