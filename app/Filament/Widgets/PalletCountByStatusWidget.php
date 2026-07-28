<?php

namespace App\Filament\Widgets;

use App\Enums\PalletStatus;
use App\Models\Pallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PalletCountByStatusWidget extends BaseWidget
{
    public function getColumns(): int
    {
        return 5;
    }
    protected function getStats(): array
    {
        return collect(PalletStatus::cases())->map(function (PalletStatus $status) {
            $count = Pallet::where('status', $status->value)->count();

            return Stat::make($status->getLabel(), $count)
                ->color($status->getColor())
                ->icon($status->getIcon());
        })->toArray();
    }
}
