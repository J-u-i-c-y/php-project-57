<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('assigned_to_id') && $this->input('assigned_to_id') === '') {
            $this->merge(['assigned_to_id' => null]);
        }

        if ($this->has('labels')) {
            $filteredLabels = array_filter($this->input('labels'), fn($label) => $label !== null);
            $this->merge(['labels' => $filteredLabels]);
        }
    }
}
