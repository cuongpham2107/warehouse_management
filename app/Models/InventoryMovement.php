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
        'from_location_code',
        'to_location_code',
        'movement_date',
        'notes',
        'performed_by',
        'device_type',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'performed_by' => 'integer',
    ];
    /**
     * Get the pallet that was moved
     */
    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }


    /**
     * Get the user who performed the movement
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
