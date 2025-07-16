<?php

namespace App\Enums;

enum ShipmentItemStatus: string
{
    case LOADING = 'loading';
    case LOADED = 'loaded';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOADING => 'Đang chất hàng',
            self::LOADED => 'Đã chất lên xe',
            self::SHIPPED => 'Đã xuất kho',
            self::DELIVERED => 'Đã giao hàng',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LOADING => 'warning',
            self::LOADED => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::LOADING => 'heroicon-o-truck',
            self::LOADED => 'heroicon-o-truck',
            self::SHIPPED => 'heroicon-o-arrow-right-circle',
            self::DELIVERED => 'heroicon-o-check-circle',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
