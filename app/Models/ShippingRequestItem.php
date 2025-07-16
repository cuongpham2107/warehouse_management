<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_request_id',
        'crate_id',
        'quantity_requested',
        'quantity_shipped',
        'notes',
    ];

    protected $casts = [
        'shipping_request_id' => 'integer',
        'crate_id' => 'integer',
        'quantity_requested' => 'integer',
        'quantity_shipped' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shipping request for this item.
     */
    public function shippingRequest()
    {
        return $this->belongsTo(ShippingRequest::class);
    }

    /**
     * Get the crate for this item.
     */
    public function crate()
    {
        return $this->belongsTo(Crate::class);
    }


    /**
     * Get remaining quantity to ship.
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_requested - $this->quantity_shipped;
    }

    /**
     * Check if item is fully shipped.
     */
    public function isFullyShipped()
    {
        return $this->quantity_shipped >= $this->quantity_requested;
    }

    /**
     * Check if item is partially shipped.
     */
    public function isPartiallyShipped()
    {
        return $this->quantity_shipped > 0 && $this->quantity_shipped < $this->quantity_requested;
    }


    /**
     * Ship a specific quantity.
     */
    public function ship($quantity)
    {
        $newShippedQuantity = min($this->quantity_shipped + $quantity, $this->quantity_requested);
        
        $this->update([
            'quantity_shipped' => $newShippedQuantity,
        ]);
    }
}
