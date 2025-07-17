<?php

namespace App\Models;

use App\Enums\CrateStatus;
use App\Enums\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crate extends Model
{
    use HasFactory;

    protected $fillable = [
        'crate_id',
        'receiving_plan_id',
        'description',
        'pieces',
        'type',
        'gross_weight',
        'dimensions_length',
        'dimensions_width',
        'dimensions_height',
        'status',
        'barcode',
    ];

    protected $casts = [
        'receiving_plan_id' => 'integer',
        'pieces' => 'integer',
        'gross_weight' => 'decimal:2',
        'type' => PackingType::class,
        'dimensions_length' => 'decimal:2',
        'dimensions_width' => 'decimal:2',
        'dimensions_height' => 'decimal:2',
        'status' => CrateStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function pallet()
    {
        return $this->hasOne(Pallet::class, 'crate_id');
    }
    /**
     * Get the receiving plan for this crate.
     */
    public function receivingPlan()
    {
        return $this->belongsTo(ReceivingPlan::class);
    }

    /**
     * Get all inventory movements for this crate.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get all shipment items for this crate.
     */
    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Scope a query to only include planned crates.
     */
    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    /**
    * Phạm vi truy vấn chỉ bao gồm các thùng hàng đã được kiểm tra (checked-in).
     */
    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    /**
     * Scope a query to only include checked-out crates.
     */
    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out');
    }

    /**
     * Scope a query to only include shipped crates.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to filter by receiving plan.
     */
    public function scopeForReceivingPlan($query, $planId)
    {
        return $query->where('receiving_plan_id', $planId);
    }

    /**
     * Calculate volume of the crate.
     */
    public function getVolumeAttribute()
    {
        if (!$this->dimensions_length || !$this->dimensions_width || !$this->dimensions_height) {
            return null;
        }

        return $this->dimensions_length * $this->dimensions_width * $this->dimensions_height;
    }

    /**
     * Get full dimensions string.
     */
    public function getDimensionsStringAttribute()
    {
        if (!$this->dimensions_length || !$this->dimensions_width || !$this->dimensions_height) {
            return 'N/A';
        }

        return "{$this->dimensions_length} x {$this->dimensions_width} x {$this->dimensions_height} cm";
    }

    /**
     * Check if crate is available for shipping.
     */
    public function isAvailableForShipping()
    {
        return $this->status === 'checked_in' && $this->pallet && $this->pallet->status === 'stored';
    }

    /**
     * Check if crate has barcode.
     */
    public function hasBarcode()
    {
        return !empty($this->barcode);
    }
}
