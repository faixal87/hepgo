<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Emel')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('No. Telefon')
                    ->toggleable(),

                TextColumn::make('role_display')
                    ->label('Peranan')
                    ->badge()
                    ->getStateUsing(fn (User $record): string => $record->roles
                        ->pluck('name')
                        ->map(fn (string $role): string => config("hep.roles.{$role}", $role))
                        ->join(', ') ?: '-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (UserStatus $state): string => $state->label())
                    ->color(fn (UserStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('Log Masuk Terakhir')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dicipta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(UserStatus::options()),

                SelectFilter::make('role')
                    ->label('Peranan')
                    ->options(config('hep.roles'))
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['value'] ?? null, fn (Builder $query, string $role): Builder => $query
                            ->whereHas('roles', fn (Builder $query): Builder => $query->where('name', $role)))),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
