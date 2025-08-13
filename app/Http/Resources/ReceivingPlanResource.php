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
                return $this->crates->map(function ($crate) {
                    return [
                        'id' => $crate->id,
                        'crate_code' => $crate->crate_id,
                        'description'=> $crate->description,
                        'pcs' => $crate->pcs,
                        'pieces' => $crate->pieces,
                        'gross_weight' => $crate->gross_weight,
                        'status' => $crate->status->value,
                    ];
                });
            }),
        ];
    }
}
