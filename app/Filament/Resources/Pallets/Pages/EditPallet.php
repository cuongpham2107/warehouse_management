<?php

namespace App\Filament\Resources\Pallets\Pages;

use App\Filament\Resources\Pallets\PalletResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPallet extends EditRecord
{
    protected static string $resource = PalletResource::class;

    public function getTitle(): string
    {
        return 'Sửa pallet';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Xem'),
            DeleteAction::make()->label('Xóa'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Lưu');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Hủy');
    }
}
