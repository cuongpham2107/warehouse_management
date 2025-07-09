<?php

namespace App\Filament\Resources\Crates\Pages;

use App\Filament\Resources\Crates\CrateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCrate extends EditRecord
{
    protected static string $resource = CrateResource::class;

    public function getTitle(): string
    {
        return 'Sửa thùng hàng';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Xem'),
            DeleteAction::make()->label('Xóa'),
        ];
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()->label('Lưu');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Hủy');
    }
}
