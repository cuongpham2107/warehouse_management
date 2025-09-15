<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class PalletStatsWidget extends Widget
{
    // protected static string $view = 'filament.widgets.pallet-stats-widget';
    
    protected static ?string $heading = 'Thống kê Pallet theo nhà cung cấp';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $stats = DB::table('pallet_with_info')
            ->join('vendors', 'pallet_with_info.vendor_id', '=', 'vendors.id')
            ->whereNotNull('pallet_with_info.vendor_id')
            ->select([
                'vendors.vendor_name',
                'vendors.vendor_code',
                DB::raw('COUNT(*) as total_pallets'),
                DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "stored" THEN 1 ELSE 0 END) as stored_pallets'),
                DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "shipped" THEN 1 ELSE 0 END) as shipped_pallets'),
                DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "in_stock" THEN 1 ELSE 0 END) as in_stock_pallets'),
                DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "in_transit" THEN 1 ELSE 0 END) as in_transit_pallets'),
                DB::raw('SUM(CASE WHEN pallet_with_info.pallet_status = "damaged" THEN 1 ELSE 0 END) as damaged_pallets'),
                DB::raw('SUM(COALESCE(pallet_with_info.crate_pcs, 0)) as total_pcs'),
                DB::raw('SUM(COALESCE(pallet_with_info.crate_gross_weight, 0)) as total_weight'),
                DB::raw('COUNT(DISTINCT pallet_with_info.plan_code) as total_plans'),
            ])
            ->groupBy('vendors.id', 'vendors.vendor_name', 'vendors.vendor_code')
            ->orderByRaw('COUNT(*) DESC, vendors.vendor_name ASC')
            ->get()
            ->map(function ($item) {
                $total = $item->total_pallets;
                $stored = $item->stored_pallets;
                $shipped = $item->shipped_pallets;
                
                return [
                    'vendor_code' => $item->vendor_code,
                    'vendor_name' => $item->vendor_name,
                    'total_pallets' => $total,
                    'stored_pallets' => $stored,
                    'shipped_pallets' => $shipped,
                    'in_stock_pallets' => $item->in_stock_pallets,
                    'in_transit_pallets' => $item->in_transit_pallets,
                    'damaged_pallets' => $item->damaged_pallets,
                    'total_pcs' => number_format($item->total_pcs ?: 0),
                    'total_weight' => number_format($item->total_weight ?: 0, 2),
                    'total_plans' => $item->total_plans,
                    'storage_rate' => $total > 0 ? round(($stored / $total) * 100, 1) : 0,
                    'shipping_rate' => $total > 0 ? round(($shipped / $total) * 100, 1) : 0,
                ];
            });

        return [
            'stats' => $stats,
        ];
    }
}
