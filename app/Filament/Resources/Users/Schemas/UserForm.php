<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Maklumat Pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Emel')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('No. Telefon')
                            ->tel()
                            ->maxLength(30),

                        Select::make('status')
                            ->label('Status')
                            ->options(UserStatus::options())
                            ->default(UserStatus::ACTIVE->value)
                            ->required(),

                        Select::make('role')
                            ->label('Peranan')
                            ->options(config('hep.roles'))
                            ->searchable()
                            ->preload()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->visible(fn (): bool => auth()->user()?->can('manage roles') ?? false),

                        TextInput::make('password')
                            ->label('Kata Laluan')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),

                        TextInput::make('password_confirmation')
                            ->label('Sahkan Kata Laluan')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2),
            ]);
    }
}
