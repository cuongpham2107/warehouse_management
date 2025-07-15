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
            $this->getSaveFormAction()
                ->formId('form')
                ->icon('heroicon-o-check')
                ->label('Lưu'),
            $this->getCancelFormAction()
                ->formId('form')
                ->icon('heroicon-o-x-mark')
                ->label('Hủy'),
            ViewAction::make()
                ->label('Xem')
                ->icon('heroicon-o-eye')
                ->outlined(),
            DeleteAction::make()->label('Xóa')
                ->icon('heroicon-o-trash'),
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

    protected function getFormActions(): array
    {
        return [];
    }
}
