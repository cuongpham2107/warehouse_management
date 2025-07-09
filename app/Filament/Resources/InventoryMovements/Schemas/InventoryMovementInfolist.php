<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('pallet.id')
                    ->numeric(),
                TextEntry::make('movement_type'),
                TextEntry::make('fromLocation.id')
                    ->numeric(),
                TextEntry::make('toLocation.id')
                    ->numeric(),
                TextEntry::make('movement_date')
                    ->dateTime(),
                TextEntry::make('reference_type'),
                TextEntry::make('reference_id')
                    ->numeric(),
                TextEntry::make('performed_by')
                    ->numeric(),
                TextEntry::make('device_type'),
                TextEntry::make('device_id'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
