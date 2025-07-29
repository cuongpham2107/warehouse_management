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
            'pallet_id' => $this->pallet_id,
            'crate_id' => $this->crate_id,
            'crate' => [
                'id' => $this->crate->id,
                'crate_id' => $this->crate->crate_id,
                'status' => $this->crate->status,
            ],
            'location_code' => $this->location_code,
            'status' => $this->status,
            'checked_in_at' => $this->checked_in_at,
            'checked_in_by' => $this->checked_in_by,
            'checked_out_at' => $this->checked_out_at,
            'checked_out_by' => $this->checked_out_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
