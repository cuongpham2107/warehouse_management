<?php

namespace App\Filament\Resources\Crates\Pages;

use App\Filament\Resources\Crates\CrateResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateCrate extends CreateRecord
{
    protected static string $resource = CrateResource::class;

    public function getTitle(): string
    {
        return 'Tạo kiện hàng';
    }
    

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Tạo');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->label('Tạo & Tạo thêm');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Hủy');
    }
}
