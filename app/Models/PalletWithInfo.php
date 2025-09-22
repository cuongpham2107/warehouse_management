<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PalletWithInfo extends Model
{
    protected $table = 'pallet_with_info';

    // This is a view, so we don't allow mass assignment
    protected $fillable = [];

    // Views typically don't have timestamps
    public $timestamps = false;

    protected $casts = [
        'pallet_created_at' => 'datetime',
        'pallet_updated_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'plan_date' => 'date',
        'arrival_date' => 'datetime',
        'requested_date' => 'datetime',
        'lifting_time' => 'datetime',
        'departure_time' => 'datetime',
        'crate_pcs' => 'integer',
        'crate_gross_weight' => 'decimal:2',
        'crate_length' => 'decimal:2',
        'crate_width' => 'decimal:2',
        'crate_height' => 'decimal:2',
        'receiving_vehicle_capacity' => 'decimal:2',
        'shipping_vehicle_capacity' => 'decimal:2',
    ];

    protected $primaryKey = 'pallet_id';

    // Define relationships if needed
    public function pallet()
    {
        return $this->belongsTo(Pallet::class, 'pallet_id');
    }

    public function crate()
    {
        return $this->belongsTo(Crate::class, 'crate_id');
    }

    public function receivingPlan()
    {
        return $this->belongsTo(ReceivingPlan::class, 'receiving_plan_id');
    }

    public function shippingRequest()
    {
        return $this->belongsTo(ShippingRequest::class, 'shipping_request_id');
    }


    public function checkInBy(){
        return $this->belongsTo(User::class, 'checked_in_by');
    }
    public function getPlanDateDateAttribute()
    {
        if ($this->plan_date) {
            return $this->plan_date->format('d/m/Y');
        }
        return null;
    }
    public function getPlanDateDateTimeAttribute()
    {
        if ($this->plan_date) {
            return $this->plan_date->format('H:i');
        }
        return null;
    }

    public function getDepartureTimeDateAttribute()
    {
        if ($this->departure_time) {
            return $this->departure_time->format('d/m/Y');
        }
        return null;
    }
    public function getDepartureTimeTimeAttribute()
    {
        if ($this->departure_time) {
            return $this->departure_time->format('H:i');
        }
        return null;
    }

    // Accessor for combined crate dimensions
    public function getCrateDimensionsAttribute()
    {
        if ($this->crate_length && $this->crate_width && $this->crate_height) {
            $crate_length = (int)$this->crate_length;
            $crate_width = (int)$this->crate_width;
            $crate_height = (int)$this->crate_height;
            return "{$crate_length} x {$crate_width} x {$crate_height}";
        }
        return null;
    }

    // Accessor for crate volume
    public function getCrateVolumeAttribute()
    {
        if ($this->crate_length && $this->crate_width && $this->crate_height) {
            return $this->crate_length * $this->crate_width * $this->crate_height;
        }
        return null;
    }
}