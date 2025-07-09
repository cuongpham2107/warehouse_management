<?php

namespace App\Filament\Resources\Pallets\Pages;

use App\Filament\Resources\Pallets\PalletResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPallets extends ListRecords
{
    protected static string $resource = PalletResource::class;

    public function getTitle(): string
    {
        return 'Danh sách pallet';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tạo mới'),
        ];
    }
}
