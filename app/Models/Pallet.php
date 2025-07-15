<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PalletStatus;

class Pallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'pallet_id',
        'crate_id',
        'location_id',
        'status',
        'checked_in_at',
        'checked_in_by',
        'checked_out_at',
        'checked_out_by',
    ];

    protected $casts = [
        'crate_id' => 'integer',
        'location_id' => 'integer',
        'status' => PalletStatus::class,
        'checked_in_at' => 'datetime',
        'checked_in_by' => 'integer',
        'checked_out_at' => 'datetime',
        'checked_out_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the crate for this pallet.
     */
    public function crate()
    {
        return $this->belongsTo(Crate::class);
    }

    /**
     * Get the warehouse location for this pallet.
     */
    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    /**
     * Get all inventory movements for this pallet.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get all shipment items for this pallet.
     */
    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Get the user who checked in this pallet.
     */
    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    /**
     * Get the user who checked out this pallet.
     */
    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    /**
     * Scope a query to only include pallets in transit.
     */
    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    /**
     * Scope a query to only include stored pallets.
     */
    public function scopeStored($query)
    {
        return $query->where('status', 'stored');
    }

    /**
     * Scope a query to only include staging pallets.
     */
    public function scopeStaging($query)
    {
        return $query->where('status', 'staging');
    }

    /**
     * Scope a query to only include shipped pallets.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeAtLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Check if pallet is stored in warehouse.
     */
    public function isStored()
    {
        return $this->status === 'stored' && !is_null($this->location_id);
    }

    /**
     * Check if pallet is available for shipping.
     */
    public function isAvailableForShipping()
    {
        return in_array($this->status, ['stored', 'staging']);
    }

    /**
     * Move pallet to staging area.
     */
    public function moveToStaging(User $user)
    {
        $this->update([
            'status' => 'staging',
            'checked_out_at' => now(),
            'checked_out_by' => $user->id,
        ]);

        // Update location status to available if moving from storage
        if ($this->location) {
            $this->location->update(['status' => 'available']);
        }
    }

    /**
     * Assign pallet to location.
     */
    public function assignToLocation(WarehouseLocation $location, User $user)
    {
        $this->update([
            'location_id' => $location->id,
            'status' => 'stored',
        ]);

        $location->update(['status' => 'occupied']);

        // Log the movement
        InventoryMovement::create([
            'pallet_id' => $this->id,
            'movement_type' => 'move',
            'to_location_id' => $location->id,
            'movement_date' => now(),
            'reference_type' => 'manual',
            'performed_by' => $user->id,
            'device_type' => 'web',
        ]);
    }
}
