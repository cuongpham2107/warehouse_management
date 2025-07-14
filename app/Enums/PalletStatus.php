<?php

namespace App\Enums;

enum PalletStatus: string
{
    case IN_TRANSIT = 'in_transit';
    case STORED = 'stored';
    case SHIPPED = 'shipped';
    case DAMAGED = 'damaged';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN_TRANSIT => 'Đang vận chuyển',
            self::STORED => 'Đã lưu kho',
            self::SHIPPED => 'Đã xuất kho',
            self::DAMAGED => 'Bị hư hỏng',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::IN_TRANSIT => 'warning',
            self::STORED => 'success',
            self::SHIPPED => 'primary',
            self::DAMAGED => 'danger',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
