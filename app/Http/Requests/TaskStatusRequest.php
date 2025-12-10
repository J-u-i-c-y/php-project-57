<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $taskStatusId = $this->route('task_status')?->id;

        return [
            'name' => "required|unique:task_statuses,name,{$taskStatusId}|max:255",
        ];
    }
}
