<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceivingPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            /**
             * Mã kế hoạch
             */
            'plan_code' => $this->plan_code,
            /**
             * Ngày hàng đến
             */
            'plan_date' => $this->plan_date->format('d-m-Y H:i'),
            /**
             * Ngày giờ hạ hàng
             */
            'arrival_date' => $this->arrival_date ? $this->arrival_date->format('d-m-Y H:i') : null,
            /**
             * Trạng thái
             */
            'status' => $this->status->value,
            /**
             * Ghi chú
             */
            'notes' => $this->notes,
            /**
             * Danh sách kiện hàng
             */
            'crates' => $this->when($this->relationLoaded('crates'), function () {
                return $this->crates
                    ->where('status', 'checked_in')
                    ->map(function ($crate) {
                        return [
                            'id' => $crate->id,
                            'crate_code' => $crate->crate_id,
                            'description'=> $crate->description,
                            'pcs' => $crate->pcs,
                            'pieces' => $crate->pieces,
                            'gross_weight' => $crate->gross_weight,
                            'status' => $crate->status->value,
                        ];
                    })
                    ->values();
            }),
            /**
             * Danh sách pallet
             */
            'pallets' => $this->when($this->relationLoaded('crates'), function () {
                return $this->crates
                    ->filter(function ($crate) {
                        return $crate->pallet !== null;
                    })
                    ->map(function ($crate) {
                        return [
                            'id' => $crate->pallet->id,
                            'pallet_code' => $crate->pallet->pallet_id,
                            'crate_id' => $crate->id,
                            'crate_code' => $crate->crate_id,
                            'location_code' => $crate->pallet->location_code,
                            'status' => $crate->pallet->status->value,
                            'checked_in_at' => $crate->pallet->checked_in_at ? $crate->pallet->checked_in_at->format('d-m-Y H:i') : null,
                        ];
                    })
                    ->values();
            }),
            
        ];
    }
}
