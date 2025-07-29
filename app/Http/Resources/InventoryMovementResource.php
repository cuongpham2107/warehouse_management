<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pallet_id' => $this->pallet_id,
            'pallet' => new PalletResource($this->whenLoaded('pallet')),
            'movement_type' => $this->movement_type,
            'from_location_code' => $this->from_location_code,
            'to_location_code' => $this->to_location_code,
            'movement_date' => $this->movement_date?->toISOString(),
            'notes' => $this->notes,
            'device_type' => $this->device_type,
            'performer' => [
                'id' => $this->performer?->id,
                'name' => $this->performer?->name,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
