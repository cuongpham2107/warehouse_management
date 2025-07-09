<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_code',
        'vendor_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all receiving plans for this vendor.
     */
    public function receivingPlans()
    {
        return $this->hasMany(ReceivingPlan::class);
    }

    /**
     * Scope a query to only include active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive vendors.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
