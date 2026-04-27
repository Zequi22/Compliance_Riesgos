<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Risk;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Risk');
    }

    public function view(AuthUser $authUser, Risk $risk): bool
    {
        return $authUser->can('View:Risk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Risk');
    }

    public function update(AuthUser $authUser, Risk $risk): bool
    {
        return $authUser->can('Update:Risk');
    }

    public function delete(AuthUser $authUser, Risk $risk): bool
    {
        return $authUser->can('Delete:Risk');
    }

    public function restore(AuthUser $authUser, Risk $risk): bool
    {
        return $authUser->can('Restore:Risk');
    }

    public function forceDelete(AuthUser $authUser, Risk $risk): bool
    {
        return $authUser->can('ForceDelete:Risk');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Risk');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Risk');
    }

    public function replicate(AuthUser $authUser, Risk $risk): bool
    {
        return $authUser->can('Replicate:Risk');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Risk');
    }

}