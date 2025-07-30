<?php

namespace App\Filament\Widgets;

use App\Models\Vendor;
use Filament\Widgets\ChartWidget;

class WarehouseActivityWidget extends ChartWidget
{
    protected ?string $heading = 'Tổng Pallet đã nhập của các Nhà cung cấp';

    protected function getData(): array
    {
        // Lấy tất cả vendors có pallet
        $vendorData = Vendor::active()
            ->withCount(['receivingPlans as total_pallets' => function ($query) {
                $query->join('crates', 'receiving_plans.id', '=', 'crates.receiving_plan_id')
                      ->join('pallets', 'crates.id', '=', 'pallets.crate_id');
            }])
            ->having('total_pallets', '>', 0)
            ->orderBy('total_pallets', 'desc')
            ->limit(10) // Giới hạn 10 vendor hàng đầu
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        // Bảng màu cho biểu đồ doughnut
        $colors = [
            '#fca5a5', // red-300
            '#93c5fd', // blue-300
            '#fde68a', // yellow-300
            '#6ee7b7', // emerald-300
            '#c4b5fd', // violet-300
            '#fdba74', // orange-300
            '#d1d5db', // gray-300
            '#f9a8d4', // pink-300
            '#67e8f9', // cyan-300
            '#bef264', // lime-300
        ];

        foreach ($vendorData as $index => $vendor) {
            $labels[] = $vendor->vendor_name ?? $vendor->vendor_code;
            $data[] = (int) $vendor->total_pallets;
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