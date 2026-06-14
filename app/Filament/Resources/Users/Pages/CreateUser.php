<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\UserManagementService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?string $roleToSync = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->roleToSync = $data['role'] ?? null;

        abort_if(
            $this->roleToSync === 'super_admin' && ! auth()->user()?->hasRole('super_admin'),
            403,
            'Hanya Pentadbir Utama boleh mencipta akaun Pentadbir Utama.'
        );

        unset($data['role'], $data['password_confirmation']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(UserManagementService::class)->create($data, $this->roleToSync);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengguna berjaya ditambah';
    }
}
