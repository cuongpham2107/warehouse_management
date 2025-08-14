<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;
use App\Models\User;
use App\Models\ShippingRequestItem;
use App\Enums\ShippingRequestStatus;

class ShippingRequest extends Model
{
    use HasFactory, HasStates;

    protected $fillable = [
        'request_code',
        'customer_name',
        'customer_contact',
        'delivery_address',
        'requested_date',
        'lifting_time',
        'transport_garage',
        'vehicle_capacity',
        'departure_time',
        'license_plate',
        'driver_name',
        'driver_phone',
        'seal_number',
        'priority',
        'status',
        'notes',
        'status',
        'created_by',
    ];
    protected $primaryKey = 'id';

    protected $casts = [
        'requested_date' => 'datetime',
        'departure_time' => 'datetime',
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
     * Get total shipped quantity.
     */
    public function getTotalShippedQuantityAttribute()
    {
        return $this->items()->sum('quantity_shipped');
    }

    /**
     * Get days until requested date.
     */
    public function getDaysUntilRequestedAttribute()
    {
        return now()->diffInDays($this->requested_date, false);
    }
}
