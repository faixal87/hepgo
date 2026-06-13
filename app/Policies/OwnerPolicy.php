<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;

class OwnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view owners');
    }

    public function view(User $user, Owner $owner): bool
    {
        return $user->can('view owners');
    }

    public function create(User $user): bool
    {
        return $user->can('create owners');
    }

    public function update(User $user, Owner $owner): bool
    {
        return $user->can('edit owners');
    }

    public function delete(User $user, Owner $owner): bool
    {
        return $user->can('delete owners');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete owners');
    }

    public function restore(User $user, Owner $owner): bool
    {
        return $user->can('edit owners');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('edit owners');
    }

    public function forceDelete(User $user, Owner $owner): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function verify(User $user, Owner $owner): bool
    {
        return $user->can('verify owners');
    }
}
