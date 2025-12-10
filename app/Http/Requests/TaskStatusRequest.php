<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Авторизация проверяется в контроллере/политиках
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $taskStatusId = $this->route('task_status')?->id;

        return [
            'name' => "required|unique:task_statuses,name,{$taskStatusId}|max:255",
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.unique' => __('validation.unique'),
            'name.max' => __('validation.max.string'),
        ];
    }
}
