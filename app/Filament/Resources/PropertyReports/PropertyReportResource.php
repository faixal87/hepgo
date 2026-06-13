<?php

namespace App\Filament\Resources\PropertyReports;

use App\Filament\Resources\PropertyReports\Pages\CreatePropertyReport;
use App\Filament\Resources\PropertyReports\Pages\EditPropertyReport;
use App\Filament\Resources\PropertyReports\Pages\ListPropertyReports;
use App\Filament\Resources\PropertyReports\Schemas\PropertyReportForm;
use App\Filament\Resources\PropertyReports\Tables\PropertyReportsTable;
use App\Models\PropertyReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PropertyReportResource extends Resource
{
    protected static ?string $model = PropertyReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|UnitEnum|null $navigationGroup = 'Aduan & Maklum Balas';

    protected static ?string $navigationLabel = 'Aduan';

    protected static ?string $modelLabel = 'Aduan';

    protected static ?string $pluralModelLabel = 'Aduan';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return PropertyReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertyReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPropertyReports::route('/'),
            'create' => CreatePropertyReport::route('/create'),
            'edit' => EditPropertyReport::route('/{record}/edit'),
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
