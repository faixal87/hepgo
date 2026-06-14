<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use App\Services\PropertyImageService;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        app(PropertyImageService::class)->generateWebpVersionsForProperty($this->getRecord());
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        if (auth()->user()?->hasRole('staff_jabatan')) {
            return 'Rumah sewa berjaya dihantar untuk semakan HEP';
        }

        return 'Rumah sewa berjaya ditambah';
    }
}
