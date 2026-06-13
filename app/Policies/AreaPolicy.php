<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;

class AreaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view areas');
    }

    public function view(User $user, Area $area): bool
    {
        return $user->can('view areas');
    }

    public function create(User $user): bool
    {
        return $user->can('create areas');
    }

    public function update(User $user, Area $area): bool
    {
        return $user->can('edit areas');
    }

    public function delete(User $user, Area $area): bool
    {
        return $user->can('delete areas');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete areas');
    }

    public function restore(User $user, Area $area): bool
    {
        return $user->can('edit areas');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('edit areas');
    }

    public function forceDelete(User $user, Area $area): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
