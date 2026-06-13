<?php

namespace App\Filament\Resources\PropertyReports\Pages;

use App\Filament\Resources\PropertyReports\PropertyReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPropertyReports extends ListRecords
{
    protected static string $resource = PropertyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Aduan'),
        ];
    }
}
