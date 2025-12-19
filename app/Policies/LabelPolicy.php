<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LabelPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Label $label): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return Auth::check();
    }

    public function update(User $user, Label $label): bool
    {
        return Auth::check();
    }

    public function delete(?User $user, Label $label): Response
    {
        if (!$user) {
            return Response::deny(__('controllers.label_statuses_destroy_failed'));
        }
        
        if ($label->tasks()->exists()) {
            return Response::deny(__('controllers.label_statuses_destroy_failed'));
        }

        return Response::allow();
    }
}
