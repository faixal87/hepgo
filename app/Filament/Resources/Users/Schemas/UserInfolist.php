<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Butiran Pengguna')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),

                        TextEntry::make('email')
                            ->label('Emel'),

                        TextEntry::make('phone')
                            ->label('No. Telefon')
                            ->placeholder('-'),

                        TextEntry::make('roles')
                            ->label('Peranan')
                            ->badge()
                            ->getStateUsing(fn (User $record): string => $record->roles
                                ->pluck('name')
                                ->map(fn (string $role): string => config("hep.roles.{$role}", $role))
                                ->join(', ') ?: '-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (UserStatus $state): string => $state->label())
                            ->color(fn (UserStatus $state): string => $state->color()),

                        TextEntry::make('last_login_at')
                            ->label('Log Masuk Terakhir')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
