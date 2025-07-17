<?php

namespace App\Enums;

enum VehicleStatus: string
{
    // 'available', 'loading', 'in_transit', 'maintenance'
    case AVAILABLE = 'available';
    case LOADING = 'loading';
    case IN_TRANSIT = 'in_transit';
    case MAINTENANCE = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Có sẵn',
            self::LOADING => 'Đang tải hàng',
            self::IN_TRANSIT => 'Đang vận chuyển',
            self::MAINTENANCE => 'Bảo trì',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function color(): string
    {
        return match($this) {
            self::AVAILABLE => 'success',
            self::LOADING => 'warning',
            self::IN_TRANSIT => 'info',
            self::MAINTENANCE => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::AVAILABLE => 'heroicon-o-check-circle',
            self::LOADING => 'heroicon-o-arrow-up-circle',
            self::IN_TRANSIT => 'heroicon-o-truck',
            self::MAINTENANCE => 'heroicon-o-wrench-screwdriver',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::AVAILABLE => 'Xe đã sẵn sàng để sử dụng',
            self::LOADING => 'Xe đang được tải hàng',
            self::IN_TRANSIT => 'Xe đang trên đường vận chuyển',
            self::MAINTENANCE => 'Xe đang được bảo trì',
        };
    }
}