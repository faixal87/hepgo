<?php

namespace App\Filament\Widgets;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use App\Filament\Resources\Properties\PropertyResource;
use App\Models\Property;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AvailablePropertiesTableWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getTableHeading(): string
    {
        return 'Senarai Rumah Masih Kosong';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Property::query()
                    ->with(['owner', 'area'])
                    ->where('status', PropertyAvailabilityStatus::AVAILABLE->value)
                    ->where('verification_status', VerificationStatus::VERIFIED->value)
                    ->latest('updated_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Tajuk')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('owner.name')
                    ->label('Pemilik')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('area.name')
                    ->label('Kawasan')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('Harga Sewa')
                    ->money('MYR')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordUrl(fn (Property $record): string => PropertyResource::getUrl('edit', ['record' => $record]))
            ->striped()
            ->paginated([10, 20, 30, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tiada rumah masih kosong buat masa ini.')
            ->emptyStateDescription('Bila ada listing aktif yang telah disahkan, ia akan muncul di sini.');
    }
}
