<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view properties');
    }

    public function view(User $user, Property $property): bool
    {
        return $user->can('view properties');
    }

    public function create(User $user): bool
    {
        return $user->can('create properties');
    }

    public function update(User $user, Property $property): bool
    {
        return $user->can('edit properties');
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->can('delete properties');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete properties');
    }

    public function restore(User $user, Property $property): bool
    {
        return $user->can('edit properties');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('edit properties');
    }

    public function forceDelete(User $user, Property $property): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function verify(User $user, Property $property): bool
    {
        return $user->can('verify properties');
    }

    public function updateAvailability(User $user, Property $property): bool
    {
        return $user->can('update property availability');
    }
}
