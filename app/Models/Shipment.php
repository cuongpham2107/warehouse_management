<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Enums\ShipmentStatus;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_code',
        'shipping_request_id',
        'vehicle_id',
        'departure_time',
        'arrival_time',
        'total_crates',
        'total_pieces',
        'total_weight',
        'status',
        'pod_generated',
        'pod_file_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'pod_generated' => 'boolean',
        'status' => ShipmentStatus::class,
    ];



    /**
     * Get the shipping request for this shipment
     */
    public function shippingRequest(): BelongsTo
    {
        return $this->belongsTo(ShippingRequest::class);
    }

    /**
     * Get the vehicle used for this shipment
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who created this shipment
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this shipment
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all shipment items
     */
    public function shipmentItems(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Get all crates in this shipment
     */
    public function crates(): HasManyThrough
    {
        return $this->hasManyThrough(Crate::class, ShipmentItem::class, 'shipment_id', 'id', 'id', 'crate_id');
    }

    /**
     * Get all pallets in this shipment
     */
    public function pallets(): HasManyThrough
    {
        return $this->hasManyThrough(Pallet::class, ShipmentItem::class, 'shipment_id', 'id', 'id', 'pallet_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for loading shipments
     */
    public function scopeLoading(Builder $query): Builder
    {
        return $query->where('status', ShipmentStatus::LOADING);
    }

    /**
     * Scope for ready shipments
     */
    public function scopeReady(Builder $query): Builder
    {
        return $query->where('status', ShipmentStatus::READY);
    }

    /**
     * Scope for departed shipments
     */
    public function scopeDeparted(Builder $query): Builder
    {
        return $query->where('status', ShipmentStatus::DEPARTED);
    }

    /**
     * Scope for delivered shipments
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', ShipmentStatus::DELIVERED);
    }

    /**
     * Scope for returned shipments
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', ShipmentStatus::RETURNED);
    }

    /**
     * Scope for active shipments (not returned or delivered)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [ShipmentStatus::RETURNED, ShipmentStatus::DELIVERED]);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('estimated_departure', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by vehicle
     */
    public function scopeByVehicle(Builder $query, int $vehicleId): Builder
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Check if shipment is loading
     */
    public function isLoading(): bool
    {
        return $this->status === ShipmentStatus::LOADING;
    }

    /**
     * Check if shipment is ready
     */
    public function isReady(): bool
    {
        return $this->status === ShipmentStatus::READY;
    }

    /**
     * Check if shipment is departed
     */
    public function isDeparted(): bool
    {
        return $this->status === ShipmentStatus::DEPARTED;
    }

    /**
     * Check if shipment is delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === ShipmentStatus::DELIVERED;
    }

    /**
     * Check if shipment is returned
     */
    public function isReturned(): bool
    {
        return $this->status === ShipmentStatus::RETURNED;
    }

    /**
     * Check if shipment is active (not returned or delivered)
     */
    public function isActive(): bool
    {
        return !in_array($this->status, [ShipmentStatus::RETURNED, ShipmentStatus::DELIVERED]);
    }

    /**
     * Check if shipment can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [ShipmentStatus::LOADING, ShipmentStatus::READY]);
    }

    /**
     * Check if shipment can be started
     */
    public function canBeStarted(): bool
    {
        return $this->status === ShipmentStatus::READY;
    }

    /**
     * Check if shipment can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === ShipmentStatus::DEPARTED;
    }

    /**
     * Get formatted status name
     */
    public function getStatusNameAttribute(): string
    {
        return $this->status?->getLabel() ?? 'Unknown';
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->shipmentItems()->count();
    }

    /**
     * Get total crates count
     */
    public function getTotalCratesAttribute(): int
    {
        return $this->shipmentItems()->whereNotNull('crate_id')->count();
    }

    /**
     * Get total pallets count
     */
    public function getTotalPalletsAttribute(): int
    {
        return $this->shipmentItems()->whereNotNull('pallet_id')->count();
    }

    /**
     * Get estimated duration in hours
     */
    public function getEstimatedDurationAttribute(): ?float
    {
        if ($this->estimated_departure && $this->estimated_arrival) {
            return $this->estimated_departure->diffInHours($this->estimated_arrival);
        }
        return null;
    }

    /**
     * Get actual duration in hours
     */
    public function getActualDurationAttribute(): ?float
    {
        if ($this->actual_departure && $this->actual_arrival) {
            return $this->actual_departure->diffInHours($this->actual_arrival);
        }
        return null;
    }

    /**
     * Generate unique shipment code
     */
    public static function generateShipmentCode(): string
    {
        $prefix = 'SH';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new shipment
     */
    public static function createShipment(array $data): self
    {
        $data['shipment_code'] = $data['shipment_code'] ?? static::generateShipmentCode();
        $data['status'] = $data['status'] ?? ShipmentStatus::LOADING;
        
        return static::create($data);
    }

    /**
     * Start the shipment
     */
    public function start(): bool
    {
        if (!$this->canBeStarted()) {
            return false;
        }

        $this->update([
            'status' => ShipmentStatus::DEPARTED,
            'departure_time' => now(),
        ]);

        return true;
    }

    /**
     * Complete the shipment
     */
    public function complete(): bool
    {
        if (!$this->canBeCompleted()) {
            return false;
        }

        $this->update([
            'status' => ShipmentStatus::DELIVERED,
            'arrival_time' => now(),
        ]);

        return true;
    }

    /**
     * Return the shipment
     */
    public function returnShipment(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => ShipmentStatus::RETURNED,
            'notes' => $this->notes . ($reason ? "\nReturn reason: " . $reason : ''),
        ]);

        return true;
    }

    /**
     * Get shipment statistics
     */
    public static function getShipmentStats($startDate = null, $endDate = null): array
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return [
            'total_shipments' => $query->count(),
            'loading' => $query->where('status', ShipmentStatus::LOADING)->count(),
            'ready' => $query->where('status', ShipmentStatus::READY)->count(),
            'departed' => $query->where('status', ShipmentStatus::DEPARTED)->count(),
            'delivered' => $query->where('status', ShipmentStatus::DELIVERED)->count(),
            'returned' => $query->where('status', ShipmentStatus::RETURNED)->count(),
        ];
    }
}
