<?php

namespace App\Filament\Resources\WarehouseLocations;

use App\Filament\Resources\WarehouseLocations\Pages\CreateWarehouseLocation;
use App\Filament\Resources\WarehouseLocations\Pages\EditWarehouseLocation;
use App\Filament\Resources\WarehouseLocations\Pages\ListWarehouseLocations;
use App\Filament\Resources\WarehouseLocations\Pages\ViewWarehouseLocation;
use App\Filament\Resources\WarehouseLocations\Schemas\WarehouseLocationForm;
use App\Filament\Resources\WarehouseLocations\Schemas\WarehouseLocationInfolist;
use App\Filament\Resources\WarehouseLocations\Tables\WarehouseLocationsTable;
use App\Models\WarehouseLocation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WarehouseLocationResource extends Resource
{
    protected static ?string $model = WarehouseLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Quản lý kho';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'location_code';

    public static function getNavigationLabel(): string
    {
        return 'Vị trí kho';
    }

    public static function getModelLabel(): string
    {
        return 'Vị trí kho';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Vị trí kho';
    }

    public static function form(Schema $schema): Schema
    {
        return WarehouseLocationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarehouseLocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehouseLocationsTable::configure($table);
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
            'index' => ListWarehouseLocations::route('/'),
            'create' => CreateWarehouseLocation::route('/create'),
            'view' => ViewWarehouseLocation::route('/{record}'),
            'edit' => EditWarehouseLocation::route('/{record}/edit'),
        ];
    }
}
