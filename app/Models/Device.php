<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\DeviceStatus;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_code',
        'device_type',
        'device_name',
        'mac_address',
        'ip_address',
        'status',
        'last_sync_at',
        'assigned_to',
    ];

    protected $casts = [
        'device_type' => 'string',
        'status' => DeviceStatus::class,
        'last_sync_at' => 'datetime',
        'assigned_to' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user assigned to this device.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get inventory movements performed with this device.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'device_id', 'device_code');
    }

    /**
     * Scope a query to only include active devices.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by device type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Scope a query to only include PDA devices.
     */
    public function scopePda($query)
    {
        return $query->where('device_type', 'pda');
    }

    /**
     * Scope a query to only include forklift computers.
     */
    public function scopeForkliftComputer($query)
    {
        return $query->where('device_type', 'forklift_computer');
    }

    /**
     * Check if device is currently assigned.
     */
    public function isAssigned()
    {
        return !is_null($this->assigned_to);
    }

    /**
     * Update last sync time.
     */
    public function updateSyncTime()
    {
        $this->update(['last_sync_at' => now()]);
    }
}
