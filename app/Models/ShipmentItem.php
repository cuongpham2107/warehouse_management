<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'crate_id',
        'pallet_id',
        'quantity',
        'loaded_at',
        'notes',
        'loaded_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'loaded_at' => 'datetime',
    ];

    /**
     * Get the shipment this item belongs to
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Get the crate for this shipment item
     */
    public function crate(): BelongsTo
    {
        return $this->belongsTo(Crate::class);
    }

    /**
     * Get the pallet for this shipment item
     */
    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }

    /**
     * Get the user who loaded this item
     */
    public function loadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'loaded_by');
    }

    /**
     * Scope for filtering by shipment
     */
    public function scopeByShipment(Builder $query, int $shipmentId): Builder
    {
        return $query->where('shipment_id', $shipmentId);
    }

    /**
     * Scope for crate items
     */
    public function scopeCrateItems(Builder $query): Builder
    {
        return $query->whereNotNull('crate_id');
    }

    /**
     * Scope for pallet items
     */
    public function scopePalletItems(Builder $query): Builder
    {
        return $query->whereNotNull('pallet_id');
    }

    /**
     * Scope for loaded items
     */
    public function scopeLoaded(Builder $query): Builder
    {
        return $query->whereNotNull('loaded_at');
    }

    /**
     * Scope for unloaded items
     */
    public function scopeUnloaded(Builder $query): Builder
    {
        return $query->whereNull('loaded_at');
    }

    /**
     * Check if item is loaded
     */
    public function isLoaded(): bool
    {
        return !is_null($this->loaded_at);
    }

    /**
     * Check if item is a crate
     */
    public function isCrate(): bool
    {
        return !is_null($this->crate_id);
    }

    /**
     * Check if item is a pallet
     */
    public function isPallet(): bool
    {
        return !is_null($this->pallet_id);
    }

    /**
     * Get the actual item (crate or pallet)
     */
    public function getItemAttribute()
    {
        return $this->crate ?? $this->pallet;
    }

    /**
     * Get item type
     */
    public function getItemTypeAttribute(): string
    {
        return $this->crate ? 'crate' : 'pallet';
    }

    /**
     * Get item code
     */
    public function getItemCodeAttribute(): string
    {
        $item = $this->getItemAttribute();
        return $item ? $item->code : 'Unknown';
    }

    /**
     * Get item description
     */
    public function getItemDescriptionAttribute(): string
    {
        $item = $this->getItemAttribute();
        return $item ? $item->description : 'Unknown';
    }

    /**
     * Get formatted item type
     */
    public function getItemTypeNameAttribute(): string
    {
        return $this->isCrate() ? 'Crate' : 'Pallet';
    }

    /**
     * Mark the item as loaded
     */
    public function markAsLoaded(int $loadedBy = null): bool
    {
        if ($this->isLoaded()) {
            return false;
        }

        $this->update([
            'loaded_at' => now(),
            'loaded_by' => $loadedBy,
        ]);

        return true;
    }

    /**
     * Mark the item as unloaded
     */
    public function markAsUnloaded(): bool
    {
        if (!$this->isLoaded()) {
            return false;
        }

        $this->update([
            'loaded_at' => null,
            'loaded_by' => null,
        ]);

        return true;
    }

    /**
     * Create a new shipment item
     */
    public static function createItem(array $data): self
    {
        // Ensure only one of crate_id or pallet_id is set
        if (!empty($data['crate_id']) && !empty($data['pallet_id'])) {
            throw new \InvalidArgumentException('A shipment item cannot have both crate_id and pallet_id');
        }

        if (empty($data['crate_id']) && empty($data['pallet_id'])) {
            throw new \InvalidArgumentException('A shipment item must have either crate_id or pallet_id');
        }

        $data['quantity'] = $data['quantity'] ?? 1;

        return static::create($data);
    }

    /**
     * Get items for a specific shipment
     */
    public static function getItemsForShipment(int $shipmentId): Builder
    {
        return static::query()
            ->where('shipment_id', $shipmentId)
            ->with(['crate', 'pallet', 'loadedBy'])
            ->orderBy('created_at');
    }

    /**
     * Get loading progress for a shipment
     */
    public static function getLoadingProgress(int $shipmentId): array
    {
        $totalItems = static::where('shipment_id', $shipmentId)->count();
        $loadedItems = static::where('shipment_id', $shipmentId)->whereNotNull('loaded_at')->count();
        
        return [
            'total_items' => $totalItems,
            'loaded_items' => $loadedItems,
            'unloaded_items' => $totalItems - $loadedItems,
            'progress_percentage' => $totalItems > 0 ? round(($loadedItems / $totalItems) * 100, 2) : 0,
        ];
    }

    /**
     * Get items by type for a shipment
     */
    public static function getItemsByType(int $shipmentId): array
    {
        $items = static::where('shipment_id', $shipmentId);
        
        return [
            'crates' => $items->whereNotNull('crate_id')->count(),
            'pallets' => $items->whereNotNull('pallet_id')->count(),
        ];
    }

    /**
     * Check if all items in a shipment are loaded
     */
    public static function allItemsLoaded(int $shipmentId): bool
    {
        $totalItems = static::where('shipment_id', $shipmentId)->count();
        $loadedItems = static::where('shipment_id', $shipmentId)->whereNotNull('loaded_at')->count();
        
        return $totalItems > 0 && $totalItems === $loadedItems;
    }

    /**
     * Get unloaded items for a shipment
     */
    public static function getUnloadedItems(int $shipmentId): Builder
    {
        return static::query()
            ->where('shipment_id', $shipmentId)
            ->whereNull('loaded_at')
            ->with(['crate', 'pallet'])
            ->orderBy('created_at');
    }
}
