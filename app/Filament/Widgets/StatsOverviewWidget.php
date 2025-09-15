<?php

namespace App\Filament\Widgets;

use App\Models\Pallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public function getColumns(): int | array
    {
        return 3;
    }
    protected function getStats(): array
{
    // Tổng số PCS đã nhập (tất cả pallet)
    $totalPcsImported = Pallet::with('crate')->get()->sum(function ($pallet) {
        return $pallet->crate ? $pallet->crate->pcs : 0;
    });

    // Tổng số PCS đã xuất (pallet status = SHIPPED)
    $totalPcsShipped = Pallet::where('status', \App\Enums\PalletStatus::SHIPPED)
        ->with('crate')->get()->sum(function ($pallet) {
            return $pallet->crate ? $pallet->crate->pcs : 0;
        });

    // Tổng số PCS tồn trong kho (pallet status = STORED)
    $totalPcsInStock = Pallet::where('status', \App\Enums\PalletStatus::STORED)
        ->with('crate')->get()->sum(function ($pallet) {
            return $pallet->crate ? $pallet->crate->pcs : 0;
        });

    return [
        Stat::make('Tổng số PCS đã nhập', $totalPcsImported)
            ->description('Tổng số PCS của tất cả pallet đã nhập vào kho')
            ->color('primary'),

        Stat::make('Tổng số PCS đã xuất', $totalPcsShipped)
            ->description('Tổng số PCS của pallet đã xuất khỏi kho')
            ->color('success'),

        Stat::make('Tổng số PCS tồn trong kho', $totalPcsInStock)
            ->description('Tổng số PCS của pallet đang lưu trữ trong kho')
            ->color('info'),
    ];
}
}
