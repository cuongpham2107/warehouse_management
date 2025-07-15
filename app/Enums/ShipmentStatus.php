<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case LOADING = 'loading';
    case READY = 'ready';
    case DEPARTED = 'departed';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOADING => 'Chuẩn bị hàng',
            self::READY => 'Sẵn sàng',
            self::DEPARTED => 'Đã khởi hành',
            self::DELIVERED => 'Đã giao hàng',
            self::RETURNED => 'Đã trả về',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LOADING => 'warning',
            self::READY => 'info',
            self::DEPARTED => 'primary',
            self::DELIVERED => 'success',
            self::RETURNED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LOADING => 'heroicon-o-arrow-down-on-square-stack',
            self::READY => 'heroicon-o-check-badge',
            self::DEPARTED => 'heroicon-o-truck',
            self::DELIVERED => 'heroicon-o-check-circle',
            self::RETURNED => 'heroicon-o-arrow-uturn-left',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::LOADING => 'bg-yellow-100 text-yellow-800',
            self::READY => 'bg-blue-100 text-blue-800',
            self::DEPARTED => 'bg-indigo-100 text-indigo-800',
            self::DELIVERED => 'bg-green-100 text-green-800',
            self::RETURNED => 'bg-red-100 text-red-800',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
