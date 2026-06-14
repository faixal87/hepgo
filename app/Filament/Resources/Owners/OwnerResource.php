<?php

namespace App\Filament\Resources\Owners;

use App\Enums\VerificationStatus;
use App\Filament\Resources\Owners\Pages\CreateOwner;
use App\Filament\Resources\Owners\Pages\EditOwner;
use App\Filament\Resources\Owners\Pages\ListOwners;
use App\Models\Owner;
use App\Services\OwnerVerificationService;
use BackedEnum;
use Filament\Actions\Action;
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
use UnitEnum;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Pengurusan Rumah Sewa';

    protected static ?string $navigationLabel = 'Pemilik Rumah';

    protected static ?string $modelLabel = 'Pemilik Rumah';

    protected static ?string $pluralModelLabel = 'Pemilik Rumah';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canRestore(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canRestoreAny(): bool
    {
        return false;
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Maklumat Pemilik Rumah')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Pemilik')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('No. Telefon')
                        ->tel()
                        ->required()
                        ->maxLength(30),

                    TextInput::make('whatsapp_number')
                        ->label('No. WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(30),

                    TextInput::make('email')
                        ->label('Emel')
                        ->email()
                        ->maxLength(255),

                    TextInput::make('ic_number')
                        ->label('No. Kad Pengenalan')
                        ->maxLength(30),

                    Select::make('verification_status')
                        ->label('Status Pengesahan')
                        ->options(VerificationStatus::options())
                        ->disabled()
                        ->dehydrated(false),

                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(4)
                        ->columnSpanFull(),

                    Textarea::make('remarks')
                        ->label('Catatan')
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
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('No. Telefon')
                    ->searchable(),

                TextColumn::make('whatsapp_number')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Emel')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('verification_status')
                    ->label('Status Pengesahan')
                    ->badge()
                    ->formatStateUsing(fn (VerificationStatus $state): string => $state->label())
                    ->color(fn (VerificationStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('verifiedBy.name')
                    ->label('Disahkan Oleh')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('verified_at')
                    ->label('Tarikh Disahkan')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('verification_status')
                    ->label('Status Pengesahan')
                    ->options(VerificationStatus::options()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ...static::verificationActions(),
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
    public static function verificationActions(): array
    {
        return [
            Action::make('sahkan')
                ->label('Sahkan Pemilik')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (Owner $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                    && $record->verification_status !== VerificationStatus::VERIFIED)
                ->requiresConfirmation()
                ->modalHeading('Sahkan pemilik rumah')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Owner $record, array $data) => app(OwnerVerificationService::class)
                    ->updateVerification($record, VerificationStatus::VERIFIED->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Pemilik rumah berjaya disahkan.'),

            Action::make('tolak')
                ->label('Tolak Pemilik')
                ->icon(Heroicon::OutlinedXCircle)
                ->color('danger')
                ->visible(fn (Owner $record): bool => (auth()->user()?->can('verify', $record) ?? false)
                    && $record->verification_status !== VerificationStatus::REJECTED)
                ->requiresConfirmation()
                ->modalHeading('Tolak pengesahan pemilik rumah')
                ->form([
                    Textarea::make('remarks')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->action(fn (Owner $record, array $data) => app(OwnerVerificationService::class)
                    ->updateVerification($record, VerificationStatus::REJECTED->value, $data['remarks'] ?? null))
                ->successNotificationTitle('Pengesahan pemilik rumah ditolak.'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOwners::route('/'),
            'create' => CreateOwner::route('/create'),
            'edit' => EditOwner::route('/{record}/edit'),
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
