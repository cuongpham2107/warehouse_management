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
            'plan_code' => $this->plan_code,
            'vendor' => [
                'id' => $this->vendor->id,
                'name' => $this->vendor->name,
            ],
            'license_plate' => $this->license_plate,
            'plan_date' => $this->plan_date->format('Y-m-d'),
            'total_crates' => $this->total_crates,
            'total_pcs' => $this->total_pcs,
            'total_weight' => $this->total_weight,
            'status' => $this->status->value,
            'completion_percentage' => $this->completion_percentage,
            'notes' => $this->notes,
            'creator' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'crates' => $this->when($this->relationLoaded('crates'), function () {
                return $this->crates->map(function ($crate) {
                    return [
                        'id' => $crate->id,
                        'crate_id' => $crate->crate_id,
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
