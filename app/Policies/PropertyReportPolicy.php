<?php

namespace App\Policies;

use App\Models\PropertyReport;
use App\Models\User;

class PropertyReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view reports');
    }

    public function view(User $user, PropertyReport $propertyReport): bool
    {
        return $user->can('view reports');
    }

    public function create(User $user): bool
    {
        return $user->can('create reports');
    }

    public function update(User $user, PropertyReport $propertyReport): bool
    {
        return $user->can('edit reports');
    }

    public function delete(User $user, PropertyReport $propertyReport): bool
    {
        return $user->can('delete reports');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete reports');
    }

    public function restore(User $user, PropertyReport $propertyReport): bool
    {
        return $user->can('edit reports');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('edit reports');
    }

    public function forceDelete(User $user, PropertyReport $propertyReport): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function resolve(User $user, PropertyReport $propertyReport): bool
    {
        return $user->can('resolve reports');
    }

    public function export(User $user): bool
    {
        return $user->can('export reports');
    }
}
