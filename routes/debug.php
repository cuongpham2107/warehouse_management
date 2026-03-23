<?php

use App\Models\PalletWithInfo;
use Illuminate\Support\Facades\Route;

Route::get('/debug-export-data', function () {
    $items = PalletWithInfo::with(['receivingPlan.vendor', 'checkInBy'])->take(2)->get();
    
    $mapped = $items->map(function ($item) {
        return [
            $item->pallet_id ?? 'NULL',
            $item->plan_code ?? 'NULL',
            $item->receivingPlan?->vendor?->vendor_name ?? 'NULL',
            $item->crate_description ?? 'NULL',
            $item->crate_pcs ?? 'NULL',
            $item->crate_gross_weight ?? 'NULL',
            $item->crate_dimensions ?? 'NULL',
            $item->customer_name ?? 'NULL',
        ];
    });
    
    return response()->json([
        'raw_count' => $items->count(),
        'mapped_count' => $mapped->count(),
        'raw_items' => $items->toArray(),
        'mapped_items' => $mapped->toArray()
    ]);
});
