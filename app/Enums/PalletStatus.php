<?php

namespace App\Enums;

enum PalletStatus: string
{
    case IN_TRANSIT = 'in_transit';
    case STORED = 'stored';
    case IN_STOCK = 'in_stock';
    case SHIPPED = 'shipped';
    case DAMAGED = 'damaged';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN_TRANSIT => 'Đang gắn vị trí và vận chuyển',
            self::STORED => 'Đã lưu kho',
            self::IN_STOCK => 'Đang xuất kho',
            self::SHIPPED => 'Đã xuất kho',
            self::DAMAGED => 'Bị hư hỏng',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::IN_TRANSIT => 'warning',
            self::STORED => 'success',
            self::IN_STOCK => 'warning',
            self::SHIPPED => 'primary',
            self::DAMAGED => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::IN_TRANSIT => 'heroicon-m-truck',
            self::STORED => 'heroicon-m-archive-box',
            self::IN_STOCK => 'heroicon-m-inbox-arrow-down',
            self::SHIPPED => 'heroicon-m-paper-airplane',
            self::DAMAGED => 'heroicon-m-x-circle',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
