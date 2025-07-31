<?php

namespace App\Filament\Widgets;

use App\Models\Pallet;
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
            Stat::make('Tổng số Pallet nhập và xuất', Pallet::with('crate')->get()->sum(function ($pallet) {
                return $pallet->crate ? $pallet->crate->pcs : 0;
            }))
                ->description('Tổng số PCS của tất cả pallet đã nhập và đã xuất kho')
                ->color('warning'),
        ];

        // Thùng
        $crateStats = [
            Stat::make('Tổng số Pallet trong kho',  Pallet::where('status', \App\Enums\PalletStatus::STORED)->with('crate')->get()->sum(function ($pallet) {
                return $pallet->crate ? $pallet->crate->pcs : 0;
            }))
                ->description('Tổng số PCS của pallet đang lưu trữ trong kho')
                ->color('info'),
            Stat::make('Tổng số pallet đã xuất', Pallet::where('status', \App\Enums\PalletStatus::SHIPPED)->with('crate')->get()->sum(function ($pallet) {
                return $pallet->crate ? $pallet->crate->pcs : 0;
            }))
                ->description('Tổng số PCS của pallet đã xuất khỏi kho')
                ->color('success'),
        ];

        // Kế hoạch nhập kho
        $planStats = [
            Stat::make('Tổng số kế hoạch nhập kho', \App\Models\ReceivingPlan::count())
                ->description('Kế hoạch đang chờ: ' . \App\Models\ReceivingPlan::where('status', \App\Enums\ReceivingPlanStatus::PENDING)->count())
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Tổng số kế hoạch xuất kho', \App\Models\ShippingRequest::count())
                ->description('Kế hoạch đang thực hiện: ' . \App\Models\ShippingRequest::where('status',\App\Enums\ShippingRequestStatus::IN_PROGRESS)->count())
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
        ];
        return array_merge(
            $palletStats,
            $crateStats,
            $planStats,
        );
    }
}
