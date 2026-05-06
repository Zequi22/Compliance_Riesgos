<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Action;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Action');
    }

    public function view(AuthUser $authUser, Action $action): bool
    {
        return $authUser->can('View:Action');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Action');
    }

    public function update(AuthUser $authUser, Action $action): bool
    {
        return $authUser->can('Update:Action');
    }

    public function delete(AuthUser $authUser, Action $action): bool
    {
        return $authUser->can('Delete:Action');
    }

    public function restore(AuthUser $authUser, Action $action): bool
    {
        return $authUser->can('Restore:Action');
    }

    public function forceDelete(AuthUser $authUser, Action $action): bool
    {
        return $authUser->can('ForceDelete:Action');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Action');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Action');
    }

    public function replicate(AuthUser $authUser, Action $action): bool
    {
        return $authUser->can('Replicate:Action');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Action');
    }

}