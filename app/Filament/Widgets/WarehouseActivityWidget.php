<?php

namespace App\Filament\Widgets;

use App\Models\Pallet;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WarehouseActivityWidget extends ChartWidget
{
    protected ?string $heading = 'Hoạt động kho (30 ngày qua)';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $checkIns = Pallet::whereDate('checked_in_at', $date)->count();
            $checkOuts = Pallet::whereDate('checked_out_at', $date)->count();
            
            $data['check_ins'][] = $checkIns;
            $data['check_outs'][] = $checkOuts;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nhập kho',
                    'data' => $data['check_ins'],
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
                [
                    'label' => 'Xuất kho',
                    'data' => $data['check_outs'],
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}