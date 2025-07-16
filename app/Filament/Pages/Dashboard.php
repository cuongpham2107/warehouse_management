<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Quản lý kho ASGL';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\VehicleStatusChartWidget::class,
            \App\Filament\Widgets\WarehouseActivityWidget::class,
            \App\Filament\Widgets\RecentShippingRequestsWidget::class,
        ];
    }
}