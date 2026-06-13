<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view categories');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can('view categories');
    }

    public function create(User $user): bool
    {
        return $user->can('create categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('edit categories');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('delete categories');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete categories');
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->can('edit categories');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('edit categories');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
