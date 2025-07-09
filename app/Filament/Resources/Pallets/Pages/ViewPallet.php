<?php

namespace App\Filament\Resources\Pallets\Pages;

use App\Filament\Resources\Pallets\PalletResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPallet extends ViewRecord
{
    protected static string $resource = PalletResource::class;

    public function getTitle(): string
    {
        return 'Xem pallet';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Sửa'),
        ];
    }
}
