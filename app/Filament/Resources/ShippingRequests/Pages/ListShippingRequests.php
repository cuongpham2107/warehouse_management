<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShippingRequests extends ListRecords
{
    protected static string $resource = ShippingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
