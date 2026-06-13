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
