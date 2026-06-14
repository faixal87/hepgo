<?php

namespace App\Filament\Resources\Owners\Pages;

use App\Filament\Resources\Owners\OwnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOwner extends CreateRecord
{
    protected static string $resource = OwnerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        if (auth()->user()?->hasRole('staff_jabatan')) {
            return 'Maklumat pemilik rumah berjaya dihantar untuk semakan HEP';
        }

        return 'Pemilik rumah berjaya ditambah';
    }
}
