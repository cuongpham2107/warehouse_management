<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'location_code' => $this->location_code,
            'pallets_count' => $this->pallets()->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Thêm relationships khi cần
            'pallets' => PalletResource::collection($this->whenLoaded('pallets')),
        ];
    }
}
