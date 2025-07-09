<?php

namespace App\Models;

use App\Enums\VehicleType;
use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_code',
        'vehicle_type',
        'license_plate',
        'driver_name',
        'driver_phone',
        'capacity_weight',
        'capacity_volume',
        'status',
    ];

    protected $casts = [
        'vehicle_type' => VehicleType::class,
        'status' => VehicleStatus::class,
        'capacity_weight' => 'decimal:2',
        'capacity_volume' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all shipments for this vehicle.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Scope a query to only include available vehicles.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', VehicleStatus::AVAILABLE);
    }

    /**
     * Scope a query to only include loading vehicles.
     */
    public function scopeLoading($query)
    {
        return $query->where('status', VehicleStatus::LOADING);
    }

    /**
     * Scope a query to only include vehicles in transit.
     */
    public function scopeInTransit($query)
    {
        return $query->where('status', VehicleStatus::IN_TRANSIT);
    }

    /**
     * Scope a query to filter by vehicle type.
     */
    public function scopeOfType($query, VehicleType $type)
    {
        return $query->where('vehicle_type', $type);
    }

    /**
     * Scope a query to only include trucks.
     */
    public function scopeTrucks($query)
    {
        return $query->where('vehicle_type', VehicleType::TRUCK);
    }

    /**
     * Scope a query to only include containers.
     */
    public function scopeContainers($query)
    {
        return $query->where('vehicle_type', VehicleType::CONTAINER);
    }

    /**
     * Check if vehicle is available for new shipment.
     */
    public function isAvailable()
    {
        return $this->status === VehicleStatus::AVAILABLE;
    }

    /**
     * Get current shipment if vehicle is in use.
     */
    public function getCurrentShipment()
    {
        return $this->shipments()
            ->whereIn('status', ['loading', 'ready', 'departed'])
            ->latest()
            ->first();
    }

    /**
     * Calculate current load weight.
     */
    public function getCurrentLoadWeight()
    {
        $currentShipment = $this->getCurrentShipment();
        
        if (!$currentShipment) {
            return 0;
        }

        return $currentShipment->total_weight ?? 0;
    }

    /**
     * Calculate remaining capacity weight.
     */
    public function getRemainingCapacityWeightAttribute()
    {
        if (!$this->capacity_weight) {
            return null;
        }

        return $this->capacity_weight - $this->getCurrentLoadWeight();
    }

    /**
     * Check if vehicle can handle additional weight.
     */
    public function canHandleWeight($weight)
    {
        $remainingCapacity = $this->remaining_capacity_weight;
        
        return $remainingCapacity === null || $remainingCapacity >= $weight;
    }

    /**
     * Get vehicle display name.
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->vehicle_code} - {$this->license_plate}";
    }
}
