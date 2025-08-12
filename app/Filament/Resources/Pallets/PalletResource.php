<?php

namespace App\Filament\Resources\Pallets;

use App\Filament\Resources\Pallets\Pages\CreatePallet;
use App\Filament\Resources\Pallets\Pages\EditPallet;
use App\Filament\Resources\Pallets\Pages\ListPallets;
use App\Filament\Resources\Pallets\Schemas\PalletForm;
use App\Filament\Resources\Pallets\Tables\PalletsTable;
use App\Filament\Resources\Pallets\RelationManagers\ActivitiesRelationManager;
use App\Models\Pallet;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Enums\PalletStatus;

class PalletResource extends Resource
{
    protected static ?string $model = Pallet::class;

    protected static string|UnitEnum|null $navigationGroup = '4. Hàng hóa';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'pallet_id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;

    public static function getNavigationLabel(): string
    {
        return 'Pallet';
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', PalletStatus::STORED->value)->count();
    }
    public static function getModelLabel(): string
    {
        return 'Pallet';
    }

 
    public static function getPluralModelLabel(): string
    {
        return 'Pallet';
    }


    public static function form(Schema $schema): Schema
    {
        return PalletForm::configure($schema);
    }

   
    public static function table(Table $table): Table
    {
        return PalletsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPallets::route('/'),
            'create' => CreatePallet::route('/create'),
            'edit' => EditPallet::route('/{record}/edit'),
        ];
    }
}
