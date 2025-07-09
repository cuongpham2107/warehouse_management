<?php

namespace App\Filament\Resources\ShippingRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ShippingRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('request_code'),
                TextEntry::make('customer_name'),
                TextEntry::make('customer_contact'),
                TextEntry::make('requested_date')
                    ->date(),
                TextEntry::make('priority'),
                TextEntry::make('status'),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
