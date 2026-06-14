<?php

namespace App\Filament\Resources\PortalSettings\Pages;

use App\Filament\Resources\PortalSettings\PortalSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditPortalSetting extends EditRecord
{
    protected static string $resource = PortalSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Tetapan portal berjaya dikemaskini.';
    }
}
