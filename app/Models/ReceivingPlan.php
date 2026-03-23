<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReceivingPlanStatus;

class ReceivingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_code',
        'vendor_id',
        'license_plate',
        'transport_garage',
        'vehicle_capacity',
        'plan_date',
        'arrival_date',
        'total_crates',
        'total_pcs',
        'total_weight',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'plan_date' => 'date',
        'arrival_date' => 'datetime',
        'total_crates' => 'integer',
        'total_pcs' => 'integer',
        'total_weight' => 'decimal:2',
        'status' => ReceivingPlanStatus::class,
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the vendor for this receiving plan.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created this receiving plan.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

   

    /**
     * Get all crates for this receiving plan.
     */
    public function crates()
    {
        return $this->hasMany(Crate::class);
    }

    /**
     * Scope a query to only include pending plans.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in-progress plans.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed plans.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to filter by vendor.
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Calculate completion percentage.
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_crates == 0) {
            return 0;
        }

        $checkedInCrates = $this->crates()->where('status', 'checked_in')->count();
        return round(($checkedInCrates / $this->total_crates) * 100, 2);
    }

    /**
     * Check if all crates are checked in.
     */
    public function isFullyCheckedIn()
    {
        $totalCrates = $this->crates()->count();
        $checkedInCrates = $this->crates()->where('status', 'checked_in')->count();
        
        return $totalCrates > 0 && $totalCrates === $checkedInCrates;
    }

    /**
     * Update plan totals from crates.
     */
    public function updateTotals()
    {
        $totals = $this->crates()->selectRaw('
            COUNT(*) as total_crates,
            COALESCE(SUM(pcs), 0) as total_pcs,
            COALESCE(SUM(gross_weight), 0) as total_weight
        ')->first();

        $this->update([
            'total_crates' => $totals->total_crates,
            'total_pcs' => $totals->total_pcs,
            'total_weight' => $totals->total_weight,
        ]);
    }
}
