<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\LoginRedirectService;
use App\Services\UserManagementService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(
        RegisterUserRequest $request,
        UserManagementService $userService,
        LoginRedirectService $redirectService,
    ): RedirectResponse {
        $user = $userService->registerPublicUser($request->validated());

        event(new Registered($user));

        Auth::login($user);

        return redirect($redirectService->pathFor($user));
    }
}
