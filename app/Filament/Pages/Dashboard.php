<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Quản lý kho ASGL';
    
    public function getWidgets(): array
    {
        return [
            // Tổng quan hệ thống
            \App\Filament\Widgets\StatsOverviewWidget::class,
            // Hoạt động nhập/xuất pallet
            // \App\Filament\Widgets\WarehouseActivityWidget::class,

            // \App\Filament\Widgets\ShippedPalletsWidget::class,
            // Yêu cầu vận chuyển gần đây
            \App\Filament\Widgets\ReceivingPlanStatsWidget::class,
            // KPI nhân viên theo hoạt động pallet
            \App\Filament\Widgets\EmployeeKPIWidget::class,
        ];
    }
    public function getColumns(): int | array
    {
        return 2;
    }
    
}