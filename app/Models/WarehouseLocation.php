<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_code',
    ];

    protected $casts = [
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
}
