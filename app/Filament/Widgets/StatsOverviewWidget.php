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
            Stat::make('Pallet trong kho', Pallet::count())
                ->description('Tổng số PCS trong kho: ' . Pallet::with('crate')->get()->sum(function ($pallet) {
                    return $pallet->crate ? $pallet->crate->pcs : 0;
                }))
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
        ];

        // Thùng
        $crateStats = [
            Stat::make('Tổng số pallet trong kho', Pallet::where('status', \App\Enums\PalletStatus::STORED)->count())
                ->description('Tổng số PCS đang lưu: ' . Pallet::where('status', \App\Enums\PalletStatus::STORED)->with('crate')->get()->sum(function ($pallet) {
                    return $pallet->crate ? $pallet->crate->pcs : 0;
                }))
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('info'),
            Stat::make('Tổng số pallet đã xuất', Pallet::where('status', \App\Enums\PalletStatus::SHIPPED)->count())
                ->description('Tổng số PCS đã xuất: ' . Pallet::where('status', \App\Enums\PalletStatus::SHIPPED)->with('crate')->get()->sum(function ($pallet) {
                    return $pallet->crate ? $pallet->crate->pcs : 0;
                }))
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
        ];

        // Kế hoạch nhập kho
        $planStats = [
            Stat::make('Tổng số kế hoạch nhập kho', \App\Models\ReceivingPlan::count())
                ->description('Kế hoạch đang chờ: ' . \App\Models\ReceivingPlan::where('status', \App\Enums\ReceivingPlanStatus::PENDING)->count())
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Tổng số kế hoạch xuất kho', \App\Models\ShippingRequest::count())
                ->description('Tổng số PCS đã xuất: ' . \App\Models\ShippingRequest::with('items')->get()->sum(function ($request) {
                    return $request->items->sum(function ($item) {
                        return $item->crate ? $item->crate->pcs : 0;
                    });
                }))
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