<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Emel diperlukan.',
            'email.email' => 'Format emel tidak sah.',
            'password.required' => 'Kata laluan diperlukan.',
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse([
                'email' => ['Emel atau kata laluan tidak sah.'],
            ], 'Emel atau kata laluan tidak sah.', 422);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            return $this->errorResponse([], 'Akaun tidak aktif.', 403);
        }

        $token = $user->createToken('android-app')->plainTextToken;
        $user->forceFill(['last_login_at' => now()])->save();

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => (new UserResource($user))->resolve($request),
            'roles' => $user->getRoleNames()->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ], 'Log masuk berjaya.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse([], 'Log keluar berjaya.');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->successResponse(
            (new UserResource($request->user()))->resolve($request),
            'Profil pengguna berjaya dipaparkan.'
        );
    }
}
