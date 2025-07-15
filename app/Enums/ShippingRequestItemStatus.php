<?php

namespace App\Enums;

enum ShippingRequestItemStatus: string
{
   case PENDING = 'pending';
   case PICKED = 'picked';
   case SHIPPED = 'shipped';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Đang chờ xếp hàng',
            self::PICKED => 'Đã lấy hàng',
            self::SHIPPED => 'Đã xuất kho',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'info',
            self::PICKED => 'warning',
            self::SHIPPED => 'success',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-blue-100 text-blue-800',
            self::PICKED => 'bg-yellow-100 text-yellow-800',
            self::SHIPPED => 'bg-green-100 text-green-800',
        };
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-clock',
            self::PICKED => 'heroicon-m-cube',
            self::SHIPPED => 'heroicon-m-truck',
        };
    }
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
