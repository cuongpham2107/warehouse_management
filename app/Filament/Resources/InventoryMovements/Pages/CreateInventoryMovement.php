<?php

namespace App\Filament\Resources\InventoryMovements\Pages;

use App\Filament\Resources\InventoryMovements\InventoryMovementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInventoryMovement extends CreateRecord
{
    protected static string $resource = InventoryMovementResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        if($data['pallet_id'] && $data['to_location_code']) {
            // Cập nhật vị trí của pallet nếu có
            $pallet = \App\Models\Pallet::find($data['pallet_id']);
            if ($pallet) {
                $pallet->update(['location_code' => $data['to_location_code']]);
            }
        }
        return static::getModel()::create($data);
    }


}
