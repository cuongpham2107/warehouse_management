<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'pallet_id',
        'movement_type',
        'from_location_id',
        'to_location_id',
        'movement_date',
        'reference_type',
        'reference_id',
        'notes',
        'performed_by',
        'device_type',
        'device_id',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'performed_by' => 'integer',
        'reference_id' => 'integer',
    ];

    const MOVEMENT_TYPES = [
        'IN' => 'in',
        'OUT' => 'out',
        'TRANSFER' => 'transfer',
        'ADJUSTMENT' => 'adjustment',
    ];

    /**
     * Get the crate that was moved
     */
    public function crate(): BelongsTo
    {
        return $this->belongsTo(Crate::class);
    }

    /**
     * Get the pallet that was moved
     */
    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }

    /**
     * Get the source location
     */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'from_location_id');
    }

    /**
     * Get the destination location
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class, 'to_location_id');
    }

    /**
     * Get the user who performed the movement
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get the device that performed the movement
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_code');
    }

    /**
     * Scope for filtering by movement type
     */
    public function scopeByMovementType(Builder $query, string $type): Builder
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('moved_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by location
     */
    public function scopeByLocation(Builder $query, int $locationId): Builder
    {
        return $query->where(function ($q) use ($locationId) {
            $q->where('from_location_id', $locationId)
              ->orWhere('to_location_id', $locationId);
        });
    }

    /**
     * Scope for incoming movements
     */
    public function scopeIncoming(Builder $query): Builder
    {
        return $query->where('movement_type', self::MOVEMENT_TYPES['IN']);
    }

    /**
     * Scope for outgoing movements
     */
    public function scopeOutgoing(Builder $query): Builder
    {
        return $query->where('movement_type', self::MOVEMENT_TYPES['OUT']);
    }

    /**
     * Scope for transfer movements
     */
    public function scopeTransfers(Builder $query): Builder
    {
        return $query->where('movement_type', self::MOVEMENT_TYPES['TRANSFER']);
    }

    /**
     * Check if this is an incoming movement
     */
    public function isIncoming(): bool
    {
        return $this->movement_type === self::MOVEMENT_TYPES['IN'];
    }

    /**
     * Check if this is an outgoing movement
     */
    public function isOutgoing(): bool
    {
        return $this->movement_type === self::MOVEMENT_TYPES['OUT'];
    }

    /**
     * Check if this is a transfer movement
     */
    public function isTransfer(): bool
    {
        return $this->movement_type === self::MOVEMENT_TYPES['TRANSFER'];
    }

    /**
     * Check if this is an adjustment movement
     */
    public function isAdjustment(): bool
    {
        return $this->movement_type === self::MOVEMENT_TYPES['ADJUSTMENT'];
    }

    /**
     * Get formatted movement type
     */
    public function getMovementTypeNameAttribute(): string
    {
        return match ($this->movement_type) {
            self::MOVEMENT_TYPES['IN'] => 'Incoming',
            self::MOVEMENT_TYPES['OUT'] => 'Outgoing',
            self::MOVEMENT_TYPES['TRANSFER'] => 'Transfer',
            self::MOVEMENT_TYPES['ADJUSTMENT'] => 'Adjustment',
            default => 'Unknown',
        };
    }

    /**
     * Get the item that was moved (crate or pallet)
     */
    public function getMovedItemAttribute()
    {
        return $this->crate ?? $this->pallet;
    }

    /**
     * Get movement description
     */
    public function getDescriptionAttribute(): string
    {
        $item = $this->getMovedItemAttribute();
        $itemType = $this->crate ? 'Crate' : 'Pallet';
        $itemCode = $item ? $item->code : 'Unknown';
        
        $description = "{$itemType} {$itemCode}";
        
        if ($this->isTransfer()) {
            $from = $this->fromLocation->name ?? 'Unknown';
            $to = $this->toLocation->name ?? 'Unknown';
            $description .= " moved from {$from} to {$to}";
        } elseif ($this->isIncoming()) {
            $to = $this->toLocation->name ?? 'Unknown';
            $description .= " moved to {$to}";
        } elseif ($this->isOutgoing()) {
            $from = $this->fromLocation->name ?? 'Unknown';
            $description .= " moved from {$from}";
        }
        
        return $description;
    }

    /**
     * Create a new inventory movement record
     */
    public static function createMovement(array $data): self
    {
        $data['moved_at'] = $data['moved_at'] ?? now();
        
        return static::create($data);
    }

    /**
     * Get movements for a specific item
     */
    public static function getMovementsForItem(string $itemType, int $itemId): Builder
    {
        return static::query()->where($itemType . '_id', $itemId)->orderBy('moved_at', 'desc');
    }

    /**
     * Get movement statistics for a date range
     */
    public static function getMovementStats($startDate, $endDate): array
    {
        $query = static::query()->whereBetween('moved_at', [$startDate, $endDate]);
        
        return [
            'total_movements' => $query->count(),
            'incoming' => $query->where('movement_type', self::MOVEMENT_TYPES['IN'])->count(),
            'outgoing' => $query->where('movement_type', self::MOVEMENT_TYPES['OUT'])->count(),
            'transfers' => $query->where('movement_type', self::MOVEMENT_TYPES['TRANSFER'])->count(),
            'adjustments' => $query->where('movement_type', self::MOVEMENT_TYPES['ADJUSTMENT'])->count(),
        ];
    }
}
