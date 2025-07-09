<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\WarehouseLocationStatus;

class WarehouseLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_code',
        'zone',
        'rack',
        'level',
        'position',
        'max_weight',
        'max_volume',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'status' => WarehouseLocationStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all pallets stored in this location.
     */
    public function pallets()
    {
        return $this->hasMany(Pallet::class, 'location_id');
    }

    /**
     * Get inventory movements from this location.
     */
    public function movementsFrom()
    {
        return $this->hasMany(InventoryMovement::class, 'from_location_id');
    }

    /**
     * Get inventory movements to this location.
     */
    public function movementsTo()
    {
        return $this->hasMany(InventoryMovement::class, 'to_location_id');
    }

    /**
     * Scope a query to only include available locations.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include occupied locations.
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * Scope a query to filter by zone.
     */
    public function scopeInZone($query, $zone)
    {
        return $query->where('zone', $zone);
    }

    /**
     * Check if location is available for storage.
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    /**
     * Get the full location path (Zone-Rack-Level-Position).
     */
    public function getFullLocationAttribute()
    {
        return "{$this->zone}-{$this->rack}-{$this->level}-{$this->position}";
    }
}
