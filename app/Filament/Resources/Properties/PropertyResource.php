<?php

namespace App\Filament\Resources\Properties;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\VerificationStatus;
use App\Filament\Resources\Properties\Pages\CreateProperty;
use App\Filament\Resources\Properties\Pages\EditProperty;
use App\Filament\Resources\Properties\Pages\ListProperties;
use App\Filament\Resources\Properties\RelationManagers\StatusLogsRelationManager;
use App\Models\Area;
use App\Models\Category;
use App\Models\Property;
use App\Services\PropertyStatusService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use UnitEnum;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup = 'Pengurusan Rumah Sewa';

    protected static ?string $navigationLabel = 'Rumah Sewa';

    protected static ?string $modelLabel = 'Rumah Sewa';

    protected static ?string $pluralModelLabel = 'Rumah Sewa';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Pemilik')
                ->schema([
                    Select::make('owner_id')
                        ->label('Pemilik Rumah')
                        ->relationship('owner', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Maklumat Rumah')
                ->schema([
                    TextInput::make('title')
                        ->label('Tajuk')
                        ->required()
                        ->maxLength(255),

                    Select::make('area_id')
                        ->label('Kawasan')
                        ->relationship(
                            'area',
                            'name',
                            modifyQueryUsing: fn (Builder $query): Builder => $query->where('status', RecordStatus::ACTIVE->value)
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('category_id')
                        ->label('Kategori')
                        ->relationship(
                            'category',
                            'name',
                            modifyQueryUsing: fn (Builder $query): Builder => $query->where('status', RecordStatus::ACTIVE->value)
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Textarea::make('description')
                        ->label('Penerangan')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),

                    Textarea::make('address')
                        ->label('Alamat')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Harga dan Lokasi')
                ->schema([
                    TextInput::make('price')
                        ->label('Harga Sewa')
                        ->numeric()
                        ->prefix('RM')
                        ->required(),

                    TextInput::make('deposit')
                        ->label('Deposit')
                        ->numeric()
                        ->prefix('RM'),

                    TextInput::make('distance_km')
                        ->label('Jarak Dari POLIMAS (km)')
                        ->numeric()
                        ->suffix('km'),

                    Select::make('gender_preference')
                        ->label('Keutamaan Penyewa')
                        ->options(GenderPreference::options())
                        ->default(GenderPreference::ANY->value)
                        ->required(),

                    TextInput::make('total_rooms')
                        ->label('Bilangan Bilik')
                        ->numeric()
                        ->integer(),

                    TextInput::make('total_bathrooms')
                        ->label('Bilangan Bilik Air')
                        ->numeric()
                        ->integer(),

                    TextInput::make('max_occupants')
                        ->label('Maksimum Penghuni')
                        ->numeric()
                        ->integer(),

                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric(),

                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric(),

                    TextInput::make('maps_url')
                        ->label('Pautan Google Maps')
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Kemudahan')
                ->schema([
                    Toggle::make('has_parking')
                        ->label('Parking'),

                    Toggle::make('has_wifi')
                        ->label('WiFi'),

                    Toggle::make('has_washing_machine')
                        ->label('Mesin Basuh'),

                    Toggle::make('has_kitchen')
                        ->label('Dapur'),

                    Toggle::make('has_aircond')
                        ->label('Penyaman Udara'),

                    CheckboxList::make('facilities')
                        ->label('Kemudahan Tambahan')
                        ->relationship('facilities', 'name')
                        ->columns(3)
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('Status dan Pengesahan')
                ->schema([
                    Select::make('status')
                        ->label('Status Kekosongan')
                        ->options(PropertyAvailabilityStatus::options())
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('verification_status')
                        ->label('Status Pengesahan')
                        ->options(VerificationStatus::options())
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2),

            Section::make('Catatan')
                ->schema([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Gambar Rumah')
                ->schema([
                    Repeater::make('images')
                        ->label('Gambar Rumah')
                        ->relationship('images')
                        ->schema([
                            FileUpload::make('image_path')
                                ->label('Gambar Rumah')
                                ->image()
                                ->imageEditor()
                                ->imagePreviewHeight('180')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(5120)
                                ->disk('public')
                                ->directory('properties')
                                ->visibility('public')
                                ->openable()
                                ->downloadable()
                                ->required(),

                            TextInput::make('caption')
                                ->label('Kapsyen')
                                ->maxLength(255),

                            Toggle::make('is_thumbnail')
                                ->label('Gambar Utama'),

                            TextInput::make('sort_order')
                                ->label('Susunan')
                                ->numeric()
                                ->integer()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->addActionLabel('Tambah Gambar')
                        ->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Gambar Rumah'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tajuk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label('Pemilik Rumah')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('area.name')
                    ->label('Kawasan')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Harga Sewa')
                    ->money('MYR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status Kekosongan')
                    ->badge()
                    ->formatStateUsing(fn (PropertyAvailabilityStatus $state): string => $state->label())
                    ->color(fn (PropertyAvailabilityStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('verification_status')
                    ->label('Status Pengesahan')
                    ->badge()
                    ->formatStateUsing(fn (VerificationStatus $state): string => $state->label())
                    ->color(fn (VerificationStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('gender_preference')
                    ->label('Keutamaan Penyewa')
                    ->badge()
                    ->formatStateUsing(fn (GenderPreference $state): string => $state->label())
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kekosongan')
                    ->options(PropertyAvailabilityStatus::options()),

                SelectFilter::make('verification_status')
                    ->label('Status Pengesahan')
                    ->options(VerificationStatus::options()),

                SelectFilter::make('gender_preference')
                    ->label('Keutamaan Penyewa')
                    ->options(GenderPreference::options()),

                SelectFilter::make('area_id')
                    ->label('Kawasan')
                    ->options(fn (): array => Area::query()->orderBy('name')->pluck('name', 'id')->all()),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn (): array => Category::query()->orderBy('name')->pluck('name', 'id')->all()),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ...static::statusActions(),
                EditAction::make()
                    ->label('Kemaskini'),
                DeleteAction::make()
                    ->label('Padam'),
                RestoreAction::make()
                    ->label('Pulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Padam Dipilih'),
                    RestoreBulkAction::make()
                        ->label('Pulihkan Dipilih'),
                ]),
            ]);
    }

    /**
     * @return array<Action>
     */
    public static function statusActions(): array
    {
        return [
            Action::make('sahkan_rumah')
                ->label('Sahkan Rumah')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                    && $record->verification_status !== VerificationStatus::VERIFIED)
                ->requiresConfirmation()
                ->modalHeading('Sahkan rumah sewa')
                ->modalSubmitActionLabel('Sahkan')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateVerification($record, VerificationStatus::VERIFIED->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Rumah sewa berjaya disahkan.'),

            Action::make('tolak_rumah')
                ->label('Tolak Rumah')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                    && $record->verification_status !== VerificationStatus::REJECTED)
                ->requiresConfirmation()
                ->modalHeading('Tolak pengesahan rumah sewa')
                ->modalSubmitActionLabel('Tolak')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateVerification($record, VerificationStatus::REJECTED->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Pengesahan rumah sewa ditolak.'),

            Action::make('set_masih_kosong')
                ->label('Set Masih Kosong')
                ->icon(Heroicon::OutlinedCheck)
                ->color('success')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('updateAvailability', $record) ?? false)
                    && $record->status !== PropertyAvailabilityStatus::AVAILABLE)
                ->requiresConfirmation()
                ->modalHeading('Set status kepada Masih Kosong')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateAvailability($record, PropertyAvailabilityStatus::AVAILABLE->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Status rumah sewa berjaya dikemaskini.'),

            Action::make('set_telah_penuh')
                ->label('Set Telah Penuh')
                ->icon(Heroicon::OutlinedNoSymbol)
                ->color('danger')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('updateAvailability', $record) ?? false)
                    && $record->status !== PropertyAvailabilityStatus::FULL)
                ->requiresConfirmation()
                ->modalHeading('Set status kepada Telah Penuh')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateAvailability($record, PropertyAvailabilityStatus::FULL->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Status rumah sewa berjaya dikemaskini.'),

            Action::make('set_tidak_aktif')
                ->label('Set Tidak Aktif')
                ->icon(Heroicon::OutlinedPauseCircle)
                ->color('gray')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('updateAvailability', $record) ?? false)
                    && $record->status !== PropertyAvailabilityStatus::INACTIVE)
                ->requiresConfirmation()
                ->modalHeading('Set status kepada Tidak Aktif')
                ->modalSubmitActionLabel('Simpan')
                ->modalCancelActionLabel('Batal')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateAvailability($record, PropertyAvailabilityStatus::INACTIVE->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Status rumah sewa berjaya dikemaskini.'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            StatusLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'edit' => EditProperty::route('/{record}/edit'),
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
