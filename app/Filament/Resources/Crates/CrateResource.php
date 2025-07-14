<?php

namespace App\Filament\Resources\Crates;

use App\Filament\Resources\Crates\Pages\CreateCrate;
use App\Filament\Resources\Crates\Pages\EditCrate;
use App\Filament\Resources\Crates\Pages\ListCrates;
use App\Filament\Resources\Crates\Pages\ViewCrate;
use App\Filament\Resources\Crates\Schemas\CrateForm;
use App\Filament\Resources\Crates\Schemas\CrateInfolist;
use App\Filament\Resources\Crates\Tables\CratesTable;
use App\Models\Crate;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CrateResource extends Resource
{
    protected static ?string $model = Crate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = '📦 Hàng hóa';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'crate_id';

    public static function getNavigationLabel(): string
    {
        return 'Kiện hàng';
    }

    public static function getModelLabel(): string
    {
        return 'Kiện hàng';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kiện hàng';
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function form(Schema $schema): Schema
    {
        return CrateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CrateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CratesTable::configure($table);
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
            'index' => ListCrates::route('/'),
            'create' => CreateCrate::route('/create'),
            'view' => ViewCrate::route('/{record}'),
            'edit' => EditCrate::route('/{record}/edit'),
        ];
    }
}
