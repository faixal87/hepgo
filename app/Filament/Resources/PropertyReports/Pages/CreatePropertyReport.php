<?php

namespace App\Filament\Resources\PropertyReports\Pages;

use App\Filament\Resources\PropertyReports\PropertyReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyReport extends CreateRecord
{
    protected static string $resource = PropertyReportResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Aduan berjaya ditambah.';
    }
}
