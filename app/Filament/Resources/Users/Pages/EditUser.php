<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\UserManagementService;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private ?string $roleToSync = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['role'] = $this->getRecord()->roles()->value('name');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('role', $data)) {
            $this->roleToSync = $data['role'];
        }

        abort_if(
            $this->roleToSync === 'super_admin' && ! auth()->user()?->hasRole('super_admin'),
            403,
            'Hanya Pentadbir Utama boleh menetapkan peranan Pentadbir Utama.'
        );

        unset($data['role'], $data['password_confirmation']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UserManagementService::class)->update($record, $data, $this->roleToSync);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pengguna berjaya dikemaskini';
    }
}
