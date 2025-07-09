<?php

namespace App\Filament\Resources\ShippingRequests\Pages;

use App\Filament\Resources\ShippingRequests\ShippingRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditShippingRequest extends EditRecord
{
    protected static string $resource = ShippingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
