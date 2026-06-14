<?php

namespace App\Filament\Resources\PortalSettings;

use App\Filament\Resources\PortalSettings\Pages\EditPortalSetting;
use App\Filament\Resources\PortalSettings\Pages\ListPortalSettings;
use App\Models\PortalSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PortalSettingResource extends Resource
{
    protected static ?string $model = PortalSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Pengurusan Sistem';

    protected static ?string $navigationLabel = 'Tetapan Portal';

    protected static ?string $modelLabel = 'Tetapan Portal';

    protected static ?string $pluralModelLabel = 'Tetapan Portal';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Paparan Hero Portal')
                ->description('Admin boleh gantikan gambar utama di halaman depan tanpa perlu ubah kod sistem.')
                ->schema([
                    FileUpload::make('hero_image_path')
                        ->label('Gambar Hero Portal')
                        ->image()
                        ->disk('public')
                        ->directory('portal-settings')
                        ->visibility('public')
                        ->imagePreviewHeight('220')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120)
                        ->helperText('Format dibenarkan: JPG, PNG atau WebP. Saiz maksimum 5MB. Jika dikosongkan, sistem akan guna gambar lalai.'),

                    TextInput::make('hero_image_title')
                        ->label('Tajuk Gambar')
                        ->maxLength(255)
                        ->placeholder('Contoh: Peta taman sekitar Jitra')
                        ->helperText('Teks ini dipaparkan di atas gambar pada halaman utama.')
                        ->required(),

                    TextInput::make('hero_image_caption')
                        ->label('Keterangan Ringkas')
                        ->maxLength(255)
                        ->placeholder('Contoh: Klik untuk buka imej penuh dalam tab baharu.')
                        ->helperText('Teks ringkas di bawah tajuk gambar.')
                        ->required(),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('hero_image_path')
                    ->label('Gambar Hero')
                    ->disk('public')
                    ->defaultImageUrl(asset('images/taman-sekitar-jitra.png'))
                    ->square(false)
                    ->height(72),

                TextColumn::make('hero_image_title')
                    ->label('Tajuk Gambar')
                    ->searchable(),

                TextColumn::make('hero_image_caption')
                    ->label('Keterangan')
                    ->limit(60),

                TextColumn::make('updated_at')
                    ->label('Kemaskini Terakhir')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Kemaskini'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortalSettings::route('/'),
            'edit' => EditPortalSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
