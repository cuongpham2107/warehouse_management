<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case AVAILABLE = 'available';
    case LOADING = 'loading';
    case IN_TRANSIT = 'in_transit';
    case MAINTENANCE = 'maintenance';
    case IN_USE = 'in_use';
    case OUT_OF_SERVICE = 'out_of_service';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Có sẵn',
            self::LOADING => 'Đang tải hàng',
            self::IN_TRANSIT => 'Đang vận chuyển',
            self::MAINTENANCE => 'Bảo trì',
            self::IN_USE => 'Đang sử dụng',
            self::OUT_OF_SERVICE => 'Ngừng hoạt động',
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
            self::IN_USE => 'primary',
            self::OUT_OF_SERVICE => 'gray',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::AVAILABLE => 'heroicon-o-check-circle',
            self::LOADING => 'heroicon-o-arrow-up-circle',
            self::IN_TRANSIT => 'heroicon-o-truck',
            self::MAINTENANCE => 'heroicon-o-wrench-screwdriver',
            self::IN_USE => 'heroicon-o-play-circle',
            self::OUT_OF_SERVICE => 'heroicon-o-x-circle',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::AVAILABLE => 'Xe đã sẵn sàng để sử dụng',
            self::LOADING => 'Xe đang được tải hàng',
            self::IN_TRANSIT => 'Xe đang trên đường vận chuyển',
            self::MAINTENANCE => 'Xe đang được bảo trì',
            self::IN_USE => 'Xe đang được sử dụng',
            self::OUT_OF_SERVICE => 'Xe tạm ngừng hoạt động',
        };
    }

    public function isActive(): bool
    {
        return !in_array($this, [self::MAINTENANCE, self::OUT_OF_SERVICE]);
    }

    public function canBeAssigned(): bool
    {
        return $this === self::AVAILABLE;
    }
}