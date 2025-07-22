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
        'location_code',
        'status',
        'checked_in_at',
        'checked_in_by',
        'checked_out_at',
        'checked_out_by',
    ];

    protected $casts = [
        'crate_id' => 'integer',
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
     * Get all inventory movements for this pallet.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
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
     * Check if pallet is available for shipping.
     */
    public function isAvailableForShipping()
    {
        return in_array($this->status, ['stored', 'staging']);
    }


}
