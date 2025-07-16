<?php

namespace App\Filament\Widgets;

use App\Models\Vendor;
use App\Models\Vehicle;
use App\Models\Pallet;
use App\Models\ShippingRequest;
use App\Enums\VehicleStatus;
use App\Enums\PalletStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Tổng số nhà cung cấp', Vendor::count())
                ->description('Nhà cung cấp hoạt động: ' . Vendor::active()->count())
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Xe có sẵn', Vehicle::available()->count())
                ->description('Tổng số xe: ' . Vehicle::count())
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Pallet trong kho', Pallet::where('status', PalletStatus::STORED)->count())
                ->description('Tổng số pallet: ' . Pallet::count())
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),

            Stat::make('Yêu cầu vận chuyển', ShippingRequest::pending()->orWhere(function($query) {
                $query->whereState('status', \App\States\ShippingRequest\ProcessingState::class);
            })->count())
                ->description('Tổng yêu cầu: ' . ShippingRequest::count())
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('danger'),
        ];
    }
}