<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabelUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $label = $this->route('label');
        $labelId = $label ? $label->id : null;

        return [
            'name' => "required|unique:labels,name,{$labelId}|max:255",
            'description' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('controllers.unique_error_label'),
        ];
    }
}
