<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OrganizationalUnit;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationalUnitPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OrganizationalUnit');
    }

    public function view(AuthUser $authUser, OrganizationalUnit $organizationalUnit): bool
    {
        return $authUser->can('View:OrganizationalUnit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OrganizationalUnit');
    }

    public function update(AuthUser $authUser, OrganizationalUnit $organizationalUnit): bool
    {
        return $authUser->can('Update:OrganizationalUnit');
    }

    public function delete(AuthUser $authUser, OrganizationalUnit $organizationalUnit): bool
    {
        return $authUser->can('Delete:OrganizationalUnit');
    }

    public function restore(AuthUser $authUser, OrganizationalUnit $organizationalUnit): bool
    {
        return $authUser->can('Restore:OrganizationalUnit');
    }

    public function forceDelete(AuthUser $authUser, OrganizationalUnit $organizationalUnit): bool
    {
        return $authUser->can('ForceDelete:OrganizationalUnit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OrganizationalUnit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OrganizationalUnit');
    }

    public function replicate(AuthUser $authUser, OrganizationalUnit $organizationalUnit): bool
    {
        return $authUser->can('Replicate:OrganizationalUnit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OrganizationalUnit');
    }

}