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
            /**
             * Mã vị trí
             * @example LOC-001
             */
            'location_code' => $this->location_code,
            /**
             * Ngày tạo
             * @example 2023-03-15 10:00
             */
            'created_at' => $this->created_at,
            /**
             * Ngày cập nhật
             * @example 2023-03-15 10:00
             */
            'updated_at' => $this->updated_at,
        ];
    }
}
