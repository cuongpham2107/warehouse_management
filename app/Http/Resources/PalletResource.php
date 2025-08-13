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
             * Trạng thái 
             */
            'status' => $this->status,
        ];
    }
}
