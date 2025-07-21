<?php

namespace App\Filament\Widgets;

use App\Models\Pallet;
use App\Enums\PalletStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public function getColumns(): int | array
    {
        return 5;
    }
    protected function getStats(): array
    {
       

        // Pallet
        $palletStats = [
            Stat::make('Pallet trong kho', Pallet::where('status', PalletStatus::STORED)->count())
                ->description('Tổng số pallet: ' . Pallet::count())
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
        ];

        // Thùng
        $crateStats = [
            Stat::make('Tổng số thùng', \App\Models\Crate::count())
                ->description('Thùng đang lưu: ' . \App\Models\Crate::where('status', \App\Enums\CrateStatus::STORED)->count())
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('info'),
            Stat::make('Thùng đã xuất', \App\Models\Crate::where('status', \App\Enums\CrateStatus::SHIPPED)->count())
                ->description('Tổng số thùng: ' . \App\Models\Crate::count())
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
        ];

        // Kế hoạch nhập kho
        $planStats = [
            Stat::make('Tổng số kế hoạch nhập kho', \App\Models\ReceivingPlan::count())
                ->description('Kế hoạch đang chờ: ' . \App\Models\ReceivingPlan::where('status', \App\Enums\ReceivingPlanStatus::PENDING)->count())
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Kế hoạch đã hoàn thành', \App\Models\ReceivingPlan::where('status', \App\Enums\ReceivingPlanStatus::COMPLETED)->count())
                ->description('Tổng số: ' . \App\Models\ReceivingPlan::count())
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];

      
       

        return array_merge(
            $palletStats,
            $crateStats,
            $planStats,
        );
    }
}