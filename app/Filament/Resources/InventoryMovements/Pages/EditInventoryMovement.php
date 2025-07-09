<?php

namespace App\Filament\Resources\InventoryMovements\Pages;

use App\Filament\Resources\InventoryMovements\InventoryMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryMovement extends EditRecord
{
    protected static string $resource = InventoryMovementResource::class;

    public function getTitle(): string
    {
        return 'Chỉnh sửa di chuyển hàng tồn kho';
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
