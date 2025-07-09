<?php

namespace App\Enums;

enum WarehouseLocationStatus: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';
    case BLOCKED = 'blocked';

    public function getLabel(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Có sẵn',
            self::OCCUPIED => 'Đã sử dụng',
            self::MAINTENANCE => 'Bảo trì',
            self::BLOCKED => 'Bị chặn',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::OCCUPIED => 'warning',
            self::MAINTENANCE => 'info',
            self::BLOCKED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::AVAILABLE => 'heroicon-o-check-circle',
            self::OCCUPIED => 'heroicon-o-archive-box',
            self::MAINTENANCE => 'heroicon-o-wrench-screwdriver',
            self::BLOCKED => 'heroicon-o-x-circle',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-green-100 text-green-800',
            self::OCCUPIED => 'bg-yellow-100 text-yellow-800',
            self::MAINTENANCE => 'bg-blue-100 text-blue-800',
            self::BLOCKED => 'bg-red-100 text-red-800',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
