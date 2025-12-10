<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $labelId = $this->route('label')?->id;

        return [
            'name' => "required|unique:labels,name,{$labelId}|max:255",
            'description' => 'nullable|string|max:255',
        ];
    }
}
