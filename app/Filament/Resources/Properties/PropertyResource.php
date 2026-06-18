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
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
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
            Wizard::make([
                Step::make('Maklumat Pemilik')
                    ->schema([
                        Section::make('Maklumat Pemilik Rumah')
                            ->description('Masukkan nama pemilik dan nombor yang akan dihubungi oleh HEP semasa semakan.')
                            ->schema([
                                TextInput::make('owner_name')
                                    ->label('Nama Pemilik')
                                    ->placeholder('Contoh: Encik Ahmad bin Salleh')
                                    ->helperText('Gunakan nama penuh pemilik rumah seperti yang biasa digunakan semasa dihubungi.')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('owner_phone')
                                    ->label('No. Telefon')
                                    ->placeholder('Contoh: 01123456789')
                                    ->helperText('Masukkan nombor telefon yang aktif untuk panggilan biasa.')
                                    ->tel()
                                    ->required()
                                    ->maxLength(30),

                                TextInput::make('owner_whatsapp_number')
                                    ->label('No. WhatsApp')
                                    ->placeholder('Contoh: 601123456789 atau 01123456789')
                                    ->helperText('Nombor ini akan digunakan untuk pautan WhatsApp di portal.')
                                    ->tel()
                                    ->required()
                                    ->maxLength(30),

                                TextInput::make('owner_email')
                                    ->label('Emel')
                                    ->placeholder('Contoh: pemilikrumah@example.com')
                                    ->helperText('Boleh dikosongkan jika pemilik tidak menggunakan emel.')
                                    ->email()
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ]),

                Step::make('Maklumat Rumah')
                    ->schema([
                        Section::make('Maklumat Asas Rumah')
                            ->description('Terangkan rumah sewa ini secara ringkas dan mudah difahami oleh pelajar atau ibu bapa.')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tajuk')
                                    ->placeholder('Contoh: Rumah Sewa 3 Bilik Taman Siswa')
                                    ->helperText('Gunakan tajuk yang mudah difahami, contohnya jenis rumah + kawasan.')
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
                                    ->helperText('Jika kawasan/taman yang dicari tiada, klik ikon tambah (+), masukkan nama kawasan/taman dan jarak dari POLIMAS. Status kawasan baharu akan terus aktif.')
                                    ->createOptionForm([
                                        Section::make('Tambah Kawasan / Taman Baharu')
                                            ->description('Gunakan fungsi ini jika kawasan/taman belum wujud dalam senarai. Contoh: Taman Sri Aman, 0.8 km dari POLIMAS.')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Nama Kawasan / Taman')
                                                    ->placeholder('Contoh: Taman Sri Aman')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('distance_from_campus')
                                                    ->label('Jarak Dari POLIMAS (km)')
                                                    ->numeric()
                                                    ->suffix('km')
                                                    ->placeholder('Contoh: 1.5')
                                                    ->helperText('Masukkan jarak anggaran dari POLIMAS. Status kawasan akan ditetapkan sebagai aktif.'),
                                            ])
                                            ->columns(2),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $area = Area::withTrashed()->firstOrNew([
                                            'name' => trim((string) $data['name']),
                                        ]);

                                        $area->fill([
                                            'distance_from_campus' => $data['distance_from_campus'] ?? null,
                                            'status' => RecordStatus::ACTIVE,
                                        ]);

                                        if ($area->trashed()) {
                                            $area->restore();
                                        }

                                        $area->save();

                                        return (int) $area->getKey();
                                    })
                                    ->createOptionModalHeading('Tambah Kawasan / Taman Baharu')
                                    ->createOptionAction(fn (Action $action): Action => $action
                                        ->label('Add Area')
                                        ->icon(Heroicon::OutlinedPlusCircle)
                                        ->modalSubmitActionLabel('Save')
                                        ->modalCancelActionLabel('Cancel'))
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

                                ToggleButtons::make('gender_preference')
                                    ->label('Keutamaan Penyewa')
                                    ->options(GenderPreference::options())
                                    ->colors([
                                        GenderPreference::MALE->value => 'info',
                                        GenderPreference::FEMALE->value => 'danger',
                                        GenderPreference::FAMILY->value => 'warning',
                                        GenderPreference::ANY->value => 'gray',
                                    ])
                                    ->inline()
                                    ->default(GenderPreference::ANY->value)
                                    ->required()
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Penerangan')
                                    ->placeholder('Contoh: Rumah teres satu tingkat, sesuai untuk 3 hingga 4 orang pelajar, kawasan tenang dan dekat kedai makan.')
                                    ->helperText('Terangkan kelebihan utama rumah seperti saiz, suasana, sasaran penyewa dan kemudahan sekitar.')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),

                                Textarea::make('address')
                                    ->label('Alamat')
                                    ->placeholder('Contoh: No. 12, Jalan Siswa 3, Taman Siswa, 06000 Jitra, Kedah')
                                    ->helperText('Masukkan alamat atau lokasi rumah dengan sejelas mungkin supaya mudah dicari.')
                                    ->required()
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),

                Step::make('Harga & Lokasi')
                    ->schema([
                        Section::make('Harga dan Lokasi')
                            ->description('Masukkan maklumat sewaan dan lokasi yang ringkas. Koordinat teknikal tidak diperlukan di sini.')
                            ->schema([
                                TextInput::make('price')
                                    ->label('Harga Sewa')
                                    ->numeric()
                                    ->prefix('RM')
                                    ->placeholder('Contoh: 450')
                                    ->helperText('Masukkan kadar sewa bulanan dalam Ringgit Malaysia.')
                                    ->required(),

                                TextInput::make('deposit')
                                    ->label('Deposit')
                                    ->numeric()
                                    ->prefix('RM')
                                    ->placeholder('Contoh: 450')
                                    ->helperText('Boleh dikosongkan jika tiada deposit dikenakan.'),

                                TextInput::make('distance_km')
                                    ->label('Jarak Dari POLIMAS (km)')
                                    ->numeric()
                                    ->suffix('km')
                                    ->placeholder('Contoh: 1.5')
                                    ->helperText('Jika tahu anggaran jarak, masukkan untuk rujukan pelajar.')
                                    ->columnSpan(1),

                                TextInput::make('total_rooms')
                                    ->label('Bilangan Bilik')
                                    ->numeric()
                                    ->integer()
                                    ->placeholder('Contoh: 3'),

                                TextInput::make('total_bathrooms')
                                    ->label('Bilangan Bilik Air')
                                    ->numeric()
                                    ->integer()
                                    ->placeholder('Contoh: 2'),

                                TextInput::make('max_occupants')
                                    ->label('Maksimum Penghuni')
                                    ->numeric()
                                    ->integer()
                                    ->placeholder('Contoh: 4'),

                                TextInput::make('maps_url')
                                    ->label('Pautan Google Maps')
                                    ->url()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: https://maps.app.goo.gl/...')
                                    ->helperText('Buka Google Maps, cari lokasi taman atau rumah ini, tekan Kongsi > Salin pautan, kemudian tampal pautan tersebut di sini.')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ]),

                Step::make('Kemudahan')
                    ->schema([
                        Section::make('Kemudahan Rumah')
                            ->description('Tandakan kemudahan yang memang tersedia di rumah sewa ini.')
                            ->schema([
                                Checkbox::make('has_parking')
                                    ->label('Parking')
                                    ->helperText('Tandakan jika rumah ini mempunyai ruang parking.'),

                                Checkbox::make('has_wifi')
                                    ->label('WiFi')
                                    ->helperText('Tandakan jika WiFi disediakan.'),

                                Checkbox::make('has_washing_machine')
                                    ->label('Mesin Basuh')
                                    ->helperText('Tandakan jika ada mesin basuh.'),

                                Checkbox::make('has_kitchen')
                                    ->label('Dapur')
                                    ->helperText('Tandakan jika dapur boleh digunakan.'),

                                Checkbox::make('has_aircond')
                                    ->label('Penyaman Udara')
                                    ->helperText('Tandakan jika ada penyaman udara.'),
                            ])
                            ->columns(2),
                    ]),

                Step::make('Gambar & Semakan')
                    ->schema([
                        Section::make('Gambar Rumah')
                            ->description('Pilih beberapa gambar sekali gus. Selepas dipilih, anda boleh tambah kapsyen untuk setiap gambar.')
                            ->schema([
                                FileUpload::make('new_uploaded_images')
                                    ->label('Upload Images')
                                    ->image()
                                    ->multiple()
                                    ->storeFiles(false)
                                    ->imagePreviewHeight('160')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->maxFiles(12)
                                    ->live()
                                    ->helperText('Boleh pilih lebih daripada satu gambar. Format dibenarkan: JPG, PNG atau WebP. Saiz maksimum 5MB setiap gambar.')
                                    ->afterStateUpdated(fn (mixed $state, Set $set, Get $get) => self::syncNewImageEntries($state, $set, $get))
                                    ->columnSpanFull(),

                                Repeater::make('new_image_entries')
                                    ->label('Butiran Gambar Baharu')
                                    ->schema([
                                        Hidden::make('upload_key'),
                                        TextInput::make('file_name')
                                            ->label('Nama Fail')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('caption')
                                            ->label('Kapsyen')
                                            ->placeholder('Contoh: Bahagian hadapan rumah')
                                            ->maxLength(255),
                                        Toggle::make('is_thumbnail')
                                            ->label('Gambar Utama'),
                                        TextInput::make('sort_order')
                                            ->label('Susunan')
                                            ->numeric()
                                            ->integer()
                                            ->default(1),
                                    ])
                                    ->columns(2)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->itemLabel(fn (array $state): string => $state['file_name'] ?? 'Gambar Baharu')
                                    ->visible(fn (Get $get): bool => filled($get('new_uploaded_images')))
                                    ->columnSpanFull(),

                                Repeater::make('images')
                                    ->label('Gambar Tersimpan')
                                    ->relationship('images')
                                    ->hiddenOn('create')
                                    ->schema([
                                        FileUpload::make('image_path')
                                            ->label('Replace Image')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('160')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120)
                                            ->disk('public')
                                            ->directory('properties')
                                            ->visibility('public')
                                            ->openable()
                                            ->downloadable()
                                            ->helperText('Guna medan ini jika anda mahu tukar gambar yang sudah disimpan.')
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
                                    ->addable(false)
                                    ->deleteAction(fn ($action) => $action->label('Delete Image'))
                                    ->itemLabel(fn (array $state): string => $state['caption'] ?? 'Gambar Rumah')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Semakan Dalaman')
                            ->description('Selepas maklumat dihantar, HEP akan hubungi pemilik rumah dan buat semakan manual sebelum listing disahkan serta dipaparkan di portal.')
                            ->schema([
                                Textarea::make('remarks')
                                    ->label('Catatan')
                                    ->placeholder('Contoh: Perlu semak semula kadar sewa atau nombor WhatsApp pemilik.')
                                    ->helperText('Catatan ini untuk kegunaan dalaman pegawai/staf yang menyemak rekod.')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
                ->nextAction(fn (Action $action) => $action->label('Next'))
                ->previousAction(fn (Action $action) => $action->label('Back'))
                ->columnSpanFull(),
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
                Action::make('review_property')
                    ->label('Review')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->color('warning')
                    ->url(fn (Property $record): string => static::getUrl('edit', ['record' => $record]))
                    ->visible(fn (Property $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                        && $record->verification_status !== VerificationStatus::VERIFIED),
                EditAction::make()
                    ->label('Edit'),
                ActionGroup::make([
                    ...static::statusActions(),
                    RestoreAction::make()
                        ->label('Restore'),
                    DeleteAction::make()
                        ->label('Delete'),
                ])
                    ->label('More')
                    ->icon(Heroicon::OutlinedEllipsisVertical)
                    ->color('gray')
                    ->tooltip('More actions'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected'),
                    RestoreBulkAction::make()
                        ->label('Restore Selected'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->hasRole('staff_jabatan')) {
            $query->where('created_by', $user->getKey());
        }

        return $query;
    }

    /**
     * @return array<Action>
     */
    public static function statusActions(): array
    {
        return [
            Action::make('verify_property')
                ->label('Verify')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                    && $record->verification_status !== VerificationStatus::VERIFIED)
                ->requiresConfirmation()
                ->modalHeading('Sahkan rumah sewa')
                ->modalSubmitActionLabel('Verify')
                ->modalCancelActionLabel('Cancel')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateVerification($record, VerificationStatus::VERIFIED->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Rumah sewa berjaya disahkan.'),

            Action::make('reject_property')
                ->label('Reject')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                    && $record->verification_status !== VerificationStatus::REJECTED)
                ->requiresConfirmation()
                ->modalHeading('Tolak pengesahan rumah sewa')
                ->modalSubmitActionLabel('Reject')
                ->modalCancelActionLabel('Cancel')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateVerification($record, VerificationStatus::REJECTED->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Pengesahan rumah sewa ditolak.'),

            Action::make('mark_available')
                ->label('Mark Available')
                ->icon(Heroicon::OutlinedCheck)
                ->color('success')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('updateAvailability', $record) ?? false)
                    && $record->status !== PropertyAvailabilityStatus::AVAILABLE)
                ->requiresConfirmation()
                ->modalHeading('Set status kepada Masih Kosong')
                ->modalSubmitActionLabel('Save')
                ->modalCancelActionLabel('Cancel')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateAvailability($record, PropertyAvailabilityStatus::AVAILABLE->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Status rumah sewa berjaya dikemaskini.'),

            Action::make('mark_full')
                ->label('Mark Full')
                ->icon(Heroicon::OutlinedNoSymbol)
                ->color('danger')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('updateAvailability', $record) ?? false)
                    && $record->status !== PropertyAvailabilityStatus::FULL)
                ->requiresConfirmation()
                ->modalHeading('Set status kepada Telah Penuh')
                ->modalSubmitActionLabel('Save')
                ->modalCancelActionLabel('Cancel')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Property $record, array $data) => app(PropertyStatusService::class)
                    ->updateAvailability($record, PropertyAvailabilityStatus::FULL->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Status rumah sewa berjaya dikemaskini.'),

            Action::make('mark_inactive')
                ->label('Mark Inactive')
                ->icon(Heroicon::OutlinedPauseCircle)
                ->color('gray')
                ->visible(fn (Property $record): bool => (auth()->user()?->can('updateAvailability', $record) ?? false)
                    && $record->status !== PropertyAvailabilityStatus::INACTIVE)
                ->requiresConfirmation()
                ->modalHeading('Set status kepada Tidak Aktif')
                ->modalSubmitActionLabel('Save')
                ->modalCancelActionLabel('Cancel')
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

    private static function syncNewImageEntries(mixed $state, Set $set, Get $get): void
    {
        $existingEntries = collect(Arr::wrap($get('new_image_entries')))
            ->keyBy(fn (array $entry): string => (string) ($entry['upload_key'] ?? ''));

        $entries = collect(Arr::wrap($state))
            ->map(function (mixed $file, string|int $key) use ($existingEntries): array {
                $uploadKey = (string) $key;
                $existing = $existingEntries->get($uploadKey, []);
                $defaultSortOrder = is_numeric($key) ? ((int) $key + 1) : 1;

                return [
                    'upload_key' => $uploadKey,
                    'file_name' => $existing['file_name'] ?? self::resolveUploadedFileName($file),
                    'caption' => $existing['caption'] ?? null,
                    'is_thumbnail' => (bool) ($existing['is_thumbnail'] ?? false),
                    'sort_order' => $existing['sort_order'] ?? $defaultSortOrder,
                ];
            })
            ->values()
            ->all();

        $set('new_image_entries', $entries);
    }

    private static function resolveUploadedFileName(mixed $file): string
    {
        if ($file instanceof TemporaryUploadedFile) {
            return $file->getClientOriginalName();
        }

        if (is_string($file)) {
            return basename($file);
        }

        return 'Gambar Baharu';
    }
}
