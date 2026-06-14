<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use App\Services\PropertyImageService;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...PropertyResource::statusActions(),
            DeleteAction::make()
                ->label('Padam'),
            RestoreAction::make()
                ->label('Pulihkan'),
        ];
    }

    protected function afterSave(): void
    {
        app(PropertyImageService::class)->generateWebpVersionsForProperty($this->getRecord());
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Rumah sewa berjaya dikemaskini';
    }
}
