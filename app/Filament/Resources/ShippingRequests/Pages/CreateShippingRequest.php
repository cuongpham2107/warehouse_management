<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingRequest extends CreateRecord
{
    protected static string $resource = ShippingRequestResource::class;
}
