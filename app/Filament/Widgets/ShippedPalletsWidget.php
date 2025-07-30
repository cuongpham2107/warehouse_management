<?php

namespace App\Filament\Widgets;

use App\Models\Vendor;
use App\Models\ShippingRequestItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ShippedPalletsWidget extends ChartWidget
{
    protected ?string $heading = 'Tổng Pallet đã xuất của Nhà cung cấp';

    protected function getData(): array
    {
        // Truy vấn để lấy tổng pallet đã xuất theo vendor
        $vendorData = Vendor::select([
                'vendors.id',
                'vendors.vendor_name',
                'vendors.vendor_code',
                DB::raw('COUNT(DISTINCT pallets.id) as total_shipped_pallets')
            ])
            ->join('receiving_plans', 'vendors.id', '=', 'receiving_plans.vendor_id')
            ->join('crates', 'receiving_plans.id', '=', 'crates.receiving_plan_id')
            ->join('pallets', 'crates.id', '=', 'pallets.crate_id')
            ->join('shipping_request_items', 'crates.id', '=', 'shipping_request_items.crate_id')
            ->join('shipping_requests', 'shipping_request_items.shipping_request_id', '=', 'shipping_requests.id')
            ->where('shipping_request_items.quantity_shipped', '>', 0) // Chỉ lấy những item đã được xuất
            ->where('vendors.status', 'active')
            ->groupBy('vendors.id', 'vendors.vendor_name', 'vendors.vendor_code')
            ->having('total_shipped_pallets', '>', 0)
            ->orderBy('total_shipped_pallets', 'desc')
            ->limit(10) // Giới hạn 10 vendor hàng đầu
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        // Bảng màu cho biểu đồ doughnut
        $colors = [
            '#dc2626', // red-600
            '#2563eb', // blue-600
            '#ca8a04', // yellow-600
            '#059669', // emerald-600
            '#7c3aed', // violet-600
            '#ea580c', // orange-600
            '#4b5563', // gray-600
            '#db2777', // pink-600
            '#0891b2', // cyan-600
            '#65a30d', // lime-600
        ];

        foreach ($vendorData as $index => $vendor) {
            $labels[] = $vendor->vendor_name ?? $vendor->vendor_code;
            $data[] = (int) $vendor->total_shipped_pallets;
            $backgroundColors[] = $colors[$index % count($colors)];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}