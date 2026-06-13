<?php

namespace App\Filament\Resources\PropertyReports\Tables;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Models\PropertyReport;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PropertyReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label('Rumah Sewa')
                    ->placeholder('Aduan umum')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reporter_name')
                    ->label('Nama Pengadu')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('reporter_phone')
                    ->label('No. Telefon Pengadu')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('report_type')
                    ->label('Jenis Aduan')
                    ->badge()
                    ->formatStateUsing(fn (ReportType $state): string => $state->label())
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Status Aduan')
                    ->badge()
                    ->formatStateUsing(fn (ReportStatus $state): string => $state->label())
                    ->color(fn (ReportStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('handledBy.name')
                    ->label('Dikendalikan Oleh')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('handled_at')
                    ->label('Tarikh Dikendalikan')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tarikh Aduan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Aduan')
                    ->options(ReportStatus::options()),

                SelectFilter::make('report_type')
                    ->label('Jenis Aduan')
                    ->options(ReportType::options()),

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('dalam_semakan')
                    ->label('Tandakan Dalam Semakan')
                    ->icon(Heroicon::OutlinedClock)
                    ->color('info')
                    ->visible(fn (PropertyReport $record): bool => (auth()->user()?->can('resolve', $record) ?? false)
                        && $record->status !== ReportStatus::REVIEWING)
                    ->form([
                        Textarea::make('admin_remarks')
                            ->label('Catatan Admin')
                            ->rows(3),
                    ])
                    ->action(fn (PropertyReport $record, array $data) => $record->markAs(ReportStatus::REVIEWING, $data['admin_remarks'] ?? null))
                    ->successNotificationTitle('Aduan ditandakan sebagai Dalam Semakan.'),

                Action::make('selesai')
                    ->label('Tandakan Selesai')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (PropertyReport $record): bool => auth()->user()?->can('resolve', $record) ?? false)
                    ->form([
                        Textarea::make('admin_remarks')
                            ->label('Catatan Admin')
                            ->rows(3),
                    ])
                    ->action(fn (PropertyReport $record, array $data) => $record->markAs(ReportStatus::RESOLVED, $data['admin_remarks'] ?? null))
                    ->successNotificationTitle('Aduan ditandakan sebagai Selesai.'),

                Action::make('tolak')
                    ->label('Tolak Aduan')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (PropertyReport $record): bool => auth()->user()?->can('resolve', $record) ?? false)
                    ->form([
                        Textarea::make('admin_remarks')
                            ->label('Catatan Admin')
                            ->rows(3),
                    ])
                    ->action(fn (PropertyReport $record, array $data) => $record->markAs(ReportStatus::REJECTED, $data['admin_remarks'] ?? null))
                    ->successNotificationTitle('Aduan ditolak.'),

                EditAction::make()
                    ->label('Kemaskini'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Padam Dipilih'),
                ]),
            ]);
    }
}
