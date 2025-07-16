<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use Filament\Widgets\ChartWidget;

class VehicleStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Trạng thái xe';
    
    protected int | string | array $columnSpan = 1;
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $available = Vehicle::available()->count();
        $loading = Vehicle::loading()->count();
        $inTransit = Vehicle::inTransit()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Trạng thái xe',
                    'data' => [$available, $loading, $inTransit],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(251, 191, 36)',
                        'rgb(239, 68, 68)',
                    ],
                ],
            ],
            'labels' => ['Có sẵn', 'Đang tải', 'Đang vận chuyển'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}