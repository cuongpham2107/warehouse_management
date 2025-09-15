<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPalletStats extends Model
{
    // Không chỉ định table cụ thể để có thể sử dụng với fromSub
    // protected $table = 'pallet_with_info';
    
    // Disable timestamps since this is just for statistics
    public $timestamps = false;
    
    // Sử dụng id làm primary key cho aggregated data
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id',
        'vendor_name',
        'vendor_code',
        'total_pallets',
        'stored_pallets',
        'shipped_pallets',
        'in_stock_pallets',
        'in_transit_pallets',
        'damaged_pallets',
        'total_pcs',
        'total_weight',
        'total_plans',
    ];

    protected $casts = [
        'id' => 'integer',
        'total_pallets' => 'integer',
        'stored_pallets' => 'integer',
        'shipped_pallets' => 'integer',
        'in_stock_pallets' => 'integer',
        'in_transit_pallets' => 'integer',
        'damaged_pallets' => 'integer',
        'total_pcs' => 'integer',
        'total_weight' => 'decimal:2',
        'total_plans' => 'integer',
    ];
}
