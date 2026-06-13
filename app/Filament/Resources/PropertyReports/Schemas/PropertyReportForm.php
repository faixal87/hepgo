<?php

namespace App\Filament\Resources\PropertyReports\Schemas;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PropertyReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Maklumat Aduan')
                    ->schema([
                        Select::make('property_id')
                            ->label('Rumah Sewa')
                            ->relationship(
                                'property',
                                'title',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->with(['area'])->orderBy('title')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->title.($record->area ? ' - '.$record->area->name : ''))
                            ->searchable()
                            ->preload(),

                        Select::make('report_type')
                            ->label('Jenis Aduan')
                            ->options(ReportType::options())
                            ->required(),

                        Select::make('status')
                            ->label('Status Aduan')
                            ->options(ReportStatus::options())
                            ->default(ReportStatus::NEW->value)
                            ->required(),

                        Textarea::make('message')
                            ->label('Mesej Aduan')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Maklumat Pengadu')
                    ->schema([
                        TextInput::make('reporter_name')
                            ->label('Nama Pengadu')
                            ->maxLength(255),

                        TextInput::make('reporter_phone')
                            ->label('No. Telefon Pengadu')
                            ->tel()
                            ->maxLength(30),

                        TextInput::make('reporter_email')
                            ->label('Emel Pengadu')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Section::make('Tindakan HEP')
                    ->schema([
                        Select::make('handled_by')
                            ->label('Dikendalikan Oleh')
                            ->relationship('handledBy', 'name')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('handled_at')
                            ->label('Tarikh Dikendalikan')
                            ->disabled()
                            ->dehydrated(false),

                        Textarea::make('admin_remarks')
                            ->label('Catatan Admin')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
