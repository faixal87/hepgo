<?php

namespace App\Policies;

use App\Models\PortalSetting;
use App\Models\User;

class PortalSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, PortalSetting $portalSetting): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, PortalSetting $portalSetting): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, PortalSetting $portalSetting): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
