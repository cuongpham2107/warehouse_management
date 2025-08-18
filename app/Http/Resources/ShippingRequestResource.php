<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            /**
             * Mã yêu cầu
             * @example SR-001
             */
            'request_code' => $this->request_code,
            /**
             * Ngày giao hàng
             * @example 2023-03-15
             */
            'requested_date' => $this->requested_date?->format('d-m-Y H:i'),
            /**
             * Thời gian đóng hàng
             * @example 10:00
             */
            'lifting_time' => $this->lifting_time,
            /**
             * Thời gian khởi hành
             * @example 2023-03-15 10:00
             */
            'departure_time' => $this->departure_time?->format('d-m-Y H:i'),
            /**
             * Số seal
             * @example SEAL-001
             */
            'seal_number' => $this->seal_number,
            /**
             * Trạng thái
             * @example pending 
             */
            'status' => $this->status,
            /**
             * Ghi chú
             * @example Ghi chú về yêu cầu
             */
            'notes' => $this->notes,
            /**
             * Danh sách pallet
             */
            'items' => $this->when($this->relationLoaded('items'), function () {
                return $this->items->map(function ($item) {
                    return [
                        'crate_code' => $item->crate->crate_id,
                        'pallet_code' => $item->pallet->pallet_id,
                        'location_code' => $item->pallet->location_code,
                        'pieces' => $item->crate->pieces,
                        'pcs' => $item->crate->pcs,
                        'gross_weight' => $item->crate->gross_weight,
                        'status' => $item->pallet->status,
                    ];
                });
            }),
        ];
    }
}
