<?php

namespace App\Filament\Resources\WarehouseLocations\Pages;

use App\Filament\Resources\WarehouseLocations\WarehouseLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWarehouseLocation extends EditRecord
{
    protected static string $resource = WarehouseLocationResource::class;

    public function getTitle(): string
    {
        return 'Chỉnh sửa vị trí kho';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Xem'),
            DeleteAction::make()
                ->label('Xóa'),
        ];
    }
}
