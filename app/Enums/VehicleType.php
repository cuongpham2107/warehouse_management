<?php

namespace App\Enums;

enum VehicleType: string
{
    case TRUCK = 'truck';
    case CONTAINER = 'container';
    case VAN = 'van';

    public function label(): string
    {
        return match($this) {
            self::TRUCK => 'Xe tải',
            self::CONTAINER => 'Container',
            self::VAN => 'Xe van',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function description(): string
    {
        return match($this) {
            self::TRUCK => 'Xe tải chở hàng thông thường',
            self::CONTAINER => 'Container vận chuyển hàng hóa',
            self::VAN => 'Xe van giao hàng nhỏ',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TRUCK => 'heroicon-o-truck',
            self::CONTAINER => 'heroicon-o-cube',
            self::VAN => 'heroicon-o-rectangle-group',
        };
    }
}