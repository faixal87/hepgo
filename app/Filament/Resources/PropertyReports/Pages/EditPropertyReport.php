<?php

namespace App\Filament\Resources\PropertyReports\Pages;

use App\Filament\Resources\PropertyReports\PropertyReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPropertyReport extends EditRecord
{
    protected static string $resource = PropertyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Padam'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Aduan berjaya dikemaskini.';
    }
}
