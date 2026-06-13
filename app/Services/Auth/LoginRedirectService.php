<?php

namespace App\Services\Auth;

use App\Models\User;

class LoginRedirectService
{
    public function pathFor(User $user): string
    {
        if ($user->hasAnyRole(config('hep.admin_panel_roles'))) {
            return '/admin';
        }

        return route('home', absolute: false);
    }
}
