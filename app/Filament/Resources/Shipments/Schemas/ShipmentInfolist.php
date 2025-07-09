<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShipmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('shipment_code'),
                TextEntry::make('vehicle.id')
                    ->numeric(),
                TextEntry::make('shippingRequest.id')
                    ->numeric(),
                TextEntry::make('departure_time')
                    ->dateTime(),
                TextEntry::make('arrival_time')
                    ->dateTime(),
                TextEntry::make('total_crates')
                    ->numeric(),
                TextEntry::make('total_pieces')
                    ->numeric(),
                TextEntry::make('total_weight')
                    ->numeric(),
                TextEntry::make('status'),
                IconEntry::make('pod_generated')
                    ->boolean(),
                TextEntry::make('pod_file_path'),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
