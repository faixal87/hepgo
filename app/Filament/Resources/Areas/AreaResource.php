<?php

namespace App\Filament\Resources\Areas;

use App\Enums\RecordStatus;
use App\Filament\Resources\Areas\Pages\CreateArea;
use App\Filament\Resources\Areas\Pages\EditArea;
use App\Filament\Resources\Areas\Pages\ListAreas;
use App\Models\Area;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;
use UnitEnum;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Tetapan Data';

    protected static ?string $navigationLabel = 'Kawasan';

    protected static ?string $modelLabel = 'Kawasan';

    protected static ?string $pluralModelLabel = 'Kawasan';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Kawasan')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Kawasan')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule): Unique => $rule->whereNull('deleted_at'),
                        )
                        ->maxLength(255),

                    TextInput::make('distance_from_campus')
                        ->label('Jarak Dari POLIMAS')
                        ->numeric()
                        ->suffix('km'),

                    Select::make('status')
                        ->label('Status')
                        ->options(RecordStatus::options())
                        ->default(RecordStatus::ACTIVE->value)
                        ->required(),

                    Textarea::make('description')
                        ->label('Penerangan')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kawasan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('distance_from_campus')
                    ->label('Jarak Dari POLIMAS')
                    ->suffix(' km')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (RecordStatus $state): string => $state->label())
                    ->color(fn (RecordStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(RecordStatus::options()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Delete'),
                RestoreAction::make()->label('Pulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Delete Selected'),
                    RestoreBulkAction::make()->label('Pulihkan Dipilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAreas::route('/'),
            'create' => CreateArea::route('/create'),
            'edit' => EditArea::route('/{record}/edit'),
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
