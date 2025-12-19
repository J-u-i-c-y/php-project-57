<?php

namespace App\Http\Requests;

class TaskFilterRequest extends TaskRequest
{
    public function rules(): array
    {
        return [
            'filter' => 'nullable|array',
            'filter.status_id' => 'nullable|exists:task_statuses,id',
            'filter.created_by_id' => 'nullable|exists:users,id',
            'filter.assigned_to_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'filter.status_id.exists' => 'The selected status is invalid.',
            'filter.created_by_id.exists' => 'The selected creator is invalid.',
            'filter.assigned_to_id.exists' => 'The selected assignee is invalid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        
        if ($this->has('filter')) {
            $filter = $this->input('filter');
            
            foreach (['status_id', 'created_by_id', 'assigned_to_id'] as $key) {
                if (isset($filter[$key]) && $filter[$key] === '') {
                    $filter[$key] = null;
                }
            }
            
            $this->merge(['filter' => $filter]);
        }
    }
}
