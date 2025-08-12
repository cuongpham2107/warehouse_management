<?php

namespace App\Filament\Resources\ShippingRequests;

use App\Filament\Resources\ShippingRequests\Pages\CreateShippingRequest;
use App\Filament\Resources\ShippingRequests\Pages\EditShippingRequest;
use App\Filament\Resources\ShippingRequests\Pages\ListShippingRequests;
use App\Filament\Resources\ShippingRequests\Pages\ViewShippingRequest;
use App\Filament\Resources\ShippingRequests\Schemas\ShippingRequestForm;
use App\Filament\Resources\ShippingRequests\Schemas\ShippingRequestInfolist;
use App\Filament\Resources\ShippingRequests\Tables\ShippingRequestsTable;
use App\Filament\Resources\ShippingRequests\RelationManagers\ItemsRelationManager;
use App\Models\ShippingRequest;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShippingRequestResource extends Resource
{
    protected static ?string $model = ShippingRequest::class;

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'request_code';

    public static function getNavigationLabel(): string
    {
        return '2. Yêu cầu xuất kho';
    }

    public static function getModelLabel(): string
    {
        return '2. Yêu cầu xuất kho';
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::where('status', 'pending')->count();
    // }

    public static function getPluralModelLabel(): string
    {
        return '2. Yêu cầu xuất kho';
    }

    protected function getTitle(): string
    {
        return '';
    }

    public static function form(Schema $schema): Schema
    {
        return ShippingRequestForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return ShippingRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingRequests::route('/'),
            'create' => CreateShippingRequest::route('/create'),
            'edit' => EditShippingRequest::route('/{record}/edit'),
        ];
    }
}
