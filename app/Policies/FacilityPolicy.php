<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view facilities');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->can('view facilities');
    }

    public function create(User $user): bool
    {
        return $user->can('create facilities');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->can('edit facilities');
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->can('delete facilities');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete facilities');
    }

    public function restore(User $user, Facility $facility): bool
    {
        return $user->can('edit facilities');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('edit facilities');
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
