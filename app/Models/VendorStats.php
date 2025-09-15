<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorStats extends Model
{
    // Model tạm để xử lý aggregated data
    protected $table = 'vendors'; // Sử dụng table vendors làm base
    
    public $timestamps = false;
    
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
