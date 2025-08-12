<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_code',
    ];

    protected $casts = [
    ];

    /**
     * Get all pallets stored in this location.
     */
    public function pallets()
    {
        return $this->hasMany(Pallet::class, 'location_code');
    }
}
