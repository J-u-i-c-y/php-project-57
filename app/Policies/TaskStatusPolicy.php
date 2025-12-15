<?php

namespace App\Policies;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, TaskStatus $taskStatus): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, TaskStatus $taskStatus): bool
    {
        return $user !== null;
    }

    public function delete(User $user, TaskStatus $taskStatus): bool
    {
        return $user !== null && !$taskStatus->tasks()->exists();
    }

    public function restore(User $user, TaskStatus $taskStatus): bool
    {
        return false;
    }

    public function forceDelete(User $user, TaskStatus $taskStatus): bool
    {
        return false;
    }
}
