<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?string $role = null): User
    {
        return DB::transaction(function () use ($data, $role): User {
            $user = User::create($this->normalisePayload($data));

            $this->syncRole($user, $role);

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data, ?string $role = null): User
    {
        return DB::transaction(function () use ($user, $data, $role): User {
            $user->update($this->normalisePayload($data, forUpdate: true));

            if ($role !== null) {
                $this->syncRole($user, $role);
            }

            return $user->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function registerPublicUser(array $data): User
    {
        $role = $data['role'] ?? 'student';

        return $this->create($data, $role);
    }

    public function touchLastLogin(User $user): void
    {
        $user->forceFill([
            'last_login_at' => now(),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalisePayload(array $data, bool $forUpdate = false): array
    {
        $payload = Arr::only($data, [
            'name',
            'email',
            'password',
            'phone',
            'status',
        ]);

        if (empty($payload['status'])) {
            $payload['status'] = UserStatus::ACTIVE;
        }

        if (array_key_exists('password', $payload)) {
            if (blank($payload['password'])) {
                unset($payload['password']);
            } else {
                $payload['password'] = Hash::make($payload['password']);
            }
        }

        if (! $forUpdate && ! array_key_exists('password', $payload)) {
            $payload['password'] = Hash::make(str()->random(32));
        }

        return $payload;
    }

    private function syncRole(User $user, ?string $role): void
    {
        if (blank($role) || ! Role::query()->where('name', $role)->exists()) {
            return;
        }

        $user->syncRoles([$role]);
    }
}
