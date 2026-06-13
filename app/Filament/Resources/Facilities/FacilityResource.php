<?php

namespace App\Filament\Resources\Facilities;

use App\Filament\Resources\Facilities\Pages\CreateFacility;
use App\Filament\Resources\Facilities\Pages\EditFacility;
use App\Filament\Resources\Facilities\Pages\ListFacilities;
use App\Models\Facility;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Tetapan Data';

    protected static ?string $navigationLabel = 'Kemudahan';

    protected static ?string $modelLabel = 'Kemudahan';

    protected static ?string $pluralModelLabel = 'Kemudahan';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Kemudahan')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Kemudahan')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('icon')
                        ->label('Ikon')
                        ->maxLength(255),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kemudahan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('icon')
                    ->label('Ikon')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->label('Kemaskini'),
                DeleteAction::make()->label('Padam'),
                RestoreAction::make()->label('Pulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Padam Dipilih'),
                    RestoreBulkAction::make()->label('Pulihkan Dipilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFacilities::route('/'),
            'create' => CreateFacility::route('/create'),
            'edit' => EditFacility::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
