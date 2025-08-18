<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            /**
             * Pallet code 
             */
            'pallet_code' => $this->pallet_id,
            /**
             * Crate ID
             */
            'crate_id' => $this->crate_id,
            /**
             * Vị trí pallet
             */
            'location_code' => $this->location_code,
            /**
             * Trạng thái 
             */
            'status' => $this->status,
        ];
    }
}
