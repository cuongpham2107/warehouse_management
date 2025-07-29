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
            'request_code' => $this->request_code,
            'customer_name' => $this->customer_name,
            'customer_contact' => $this->customer_contact,
            'delivery_address' => $this->delivery_address,
            'requested_date' => $this->requested_date?->format('Y-m-d'),
            'departure_time' => $this->departure_time?->format('Y-m-d H:i:s'),
            'license_plate' => $this->license_plate,
            'driver_name' => $this->driver_name,
            'driver_phone' => $this->driver_phone,
            'seal_number' => $this->seal_number,
            'priority' => $this->priority,
            'status' => $this->status,
            'notes' => $this->notes,
            'total_shipped_quantity' => $this->total_shipped_quantity,
            'days_until_requested' => $this->days_until_requested,
            'creator' => new UserResource($this->whenLoaded('creator')),
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
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
