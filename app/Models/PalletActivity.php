<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PalletActivityAction;

class PalletActivity extends Model
{
    protected $fillable = [
        'pallet_id',
        'user_id',
        'action',
        'description',
        'action_time',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'action_time' => 'datetime',
        'action' => PalletActivityAction::class,
        'old_data' => 'array',
        'new_data' => 'array',
    ];
    /**
     * Get the pallet associated with this activity.
     */
    public function pallet()
    {
        return $this->belongsTo(Pallet::class);
    }
    /**
     * Get the user who performed this activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
