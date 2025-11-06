<?php

namespace App\Policies;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! is_null($user);
    }

    public function update(User $user): bool
    {
        return ! is_null($user);
    }

    public function delete(User $user, TaskStatus $taskStatus): bool
    {
        return ! is_null($user) && $taskStatus->tasks()->doesntExist();
    }
}
