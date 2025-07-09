<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ShippingRequestPriority;
use App\Enums\ShippingRequestStatus;

class ShippingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'customer_name',
        'customer_contact',
        'delivery_address',
        'requested_date',
        'priority',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'priority' => ShippingRequestPriority::class,
        'status' => ShippingRequestStatus::class,
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this shipping request.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all items for this shipping request.
     */
    public function items()
    {
        return $this->hasMany(ShippingRequestItem::class);
    }

    /**
     * Get all shipments for this shipping request.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing requests.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include ready requests.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope a query to only include shipped requests.
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include high priority requests.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope a query to only include urgent requests.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Calculate fulfillment percentage.
     */
    public function getFulfillmentPercentageAttribute()
    {
        $totalItems = $this->items()->count();
        if ($totalItems == 0) {
            return 0;
        }

        $shippedItems = $this->items()->where('status', 'shipped')->count();
        return round(($shippedItems / $totalItems) * 100, 2);
    }

    /**
     * Get total requested quantity.
     */
    public function getTotalRequestedQuantityAttribute()
    {
        return $this->items()->sum('quantity_requested');
    }

    /**
     * Get total shipped quantity.
     */
    public function getTotalShippedQuantityAttribute()
    {
        return $this->items()->sum('quantity_shipped');
    }

    /**
     * Check if request is fully fulfilled.
     */
    public function isFullyFulfilled()
    {
        $totalRequested = $this->total_requested_quantity;
        $totalShipped = $this->total_shipped_quantity;
        
        return $totalRequested > 0 && $totalRequested === $totalShipped;
    }

    /**
     * Check if request is overdue.
     */
    public function isOverdue()
    {
        return $this->requested_date->isPast() && !in_array($this->status, ['shipped', 'delivered']);
    }

    /**
     * Get days until requested date.
     */
    public function getDaysUntilRequestedAttribute()
    {
        return now()->diffInDays($this->requested_date, false);
    }
}
