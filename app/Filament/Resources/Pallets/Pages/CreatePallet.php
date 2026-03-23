<?php

namespace App\Filament\Resources\Pallets\Pages;

use App\Filament\Resources\Pallets\PalletResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreatePallet extends CreateRecord
{
    protected static string $resource = PalletResource::class;

    public function getTitle(): string
    {
        return 'Tạo pallet';
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Tạo');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()->label('Tạo & Tạo thêm');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Hủy');
    }
}
