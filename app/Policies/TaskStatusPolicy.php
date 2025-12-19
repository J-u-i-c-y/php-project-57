<?php

namespace App\Policies;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;

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
        return Auth::check();
    }

    public function update(User $user, TaskStatus $taskStatus): bool
    {
        return Auth::check();
    }

    public function delete(?User $user, TaskStatus $taskStatus): Response
    {
        if (!$user) {
            return Response::deny(__('layout.delete_error'));
        }

        if ($taskStatus->tasks()->exists()) {
            return Response::deny(__('layout.delete_error'));
        }

        return Response::allow();
    }
}
