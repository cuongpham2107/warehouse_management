<?php

namespace App\Filament\Resources\InventoryMovements\Pages;

use App\Filament\Resources\InventoryMovements\InventoryMovementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pallet;

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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // Nếu có pallet_id và to_location_id, cập nhật luôn vị trí pallet
        if (!empty($data['pallet_id']) && !empty($data['to_location_id'])) {
            $pallet = Pallet::find($data['pallet_id']);
            if ($pallet) {
                $pallet->update(['location_id' => $data['to_location_id']]);
            }
        }

        return $record;
    }
}
