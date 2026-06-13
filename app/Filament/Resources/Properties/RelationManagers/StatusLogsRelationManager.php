<?php

namespace App\Filament\Resources\Properties\RelationManagers;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    protected static bool $isLazy = false;

    protected static ?string $title = 'Log Status Rumah';

    protected static ?string $modelLabel = 'Log Status Rumah';

    protected static ?string $pluralModelLabel = 'Log Status Rumah';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('old_status')
                    ->label('Status Lama')
                    ->badge()
                    ->formatStateUsing(fn (?PropertyAvailabilityStatus $state): string => $state?->label() ?? '-')
                    ->color(fn (?PropertyAvailabilityStatus $state): string => $state?->color() ?? 'gray')
                    ->placeholder('-'),

                TextColumn::make('new_status')
                    ->label('Status Baharu')
                    ->badge()
                    ->formatStateUsing(fn (?PropertyAvailabilityStatus $state): string => $state?->label() ?? '-')
                    ->color(fn (?PropertyAvailabilityStatus $state): string => $state?->color() ?? 'gray')
                    ->placeholder('-'),

                TextColumn::make('old_verification_status')
                    ->label('Pengesahan Lama')
                    ->badge()
                    ->formatStateUsing(fn (?VerificationStatus $state): string => $state?->label() ?? '-')
                    ->color(fn (?VerificationStatus $state): string => $state?->color() ?? 'gray')
                    ->placeholder('-'),

                TextColumn::make('new_verification_status')
                    ->label('Pengesahan Baharu')
                    ->badge()
                    ->formatStateUsing(fn (?VerificationStatus $state): string => $state?->label() ?? '-')
                    ->color(fn (?VerificationStatus $state): string => $state?->color() ?? 'gray')
                    ->placeholder('-'),

                TextColumn::make('changedBy.name')
                    ->label('Dikemaskini Oleh')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('remarks')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tarikh Kemaskini')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                //
            ]);
    }
}
