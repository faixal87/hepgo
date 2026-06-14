<?php

namespace App\Filament\Resources\Owners\Pages;

use App\Filament\Resources\Owners\OwnerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditOwner extends EditRecord
{
    protected static string $resource = OwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...OwnerResource::verificationActions(),
            DeleteAction::make()
                ->label('Delete'),
            RestoreAction::make()
                ->label('Pulihkan'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pemilik rumah berjaya dikemaskini';
    }
}
