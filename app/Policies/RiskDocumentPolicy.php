<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RiskDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RiskDocument');
    }

    public function view(AuthUser $authUser, RiskDocument $riskDocument): bool
    {
        return $authUser->can('View:RiskDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RiskDocument');
    }

    public function update(AuthUser $authUser, RiskDocument $riskDocument): bool
    {
        return $authUser->can('Update:RiskDocument');
    }

    public function delete(AuthUser $authUser, RiskDocument $riskDocument): bool
    {
        return $authUser->can('Delete:RiskDocument');
    }

    public function restore(AuthUser $authUser, RiskDocument $riskDocument): bool
    {
        return $authUser->can('Restore:RiskDocument');
    }

    public function forceDelete(AuthUser $authUser, RiskDocument $riskDocument): bool
    {
        return $authUser->can('ForceDelete:RiskDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RiskDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RiskDocument');
    }

    public function replicate(AuthUser $authUser, RiskDocument $riskDocument): bool
    {
        return $authUser->can('Replicate:RiskDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RiskDocument');
    }

}