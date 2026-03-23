<?php

namespace App\Enums;

enum CrateStatus: string
{
    case PLANNED = 'planned';
    case RECEIVED = 'received';
    case CHECKED_IN = 'checked_in';
    case STORED = 'stored';
    case SHIPPED = 'shipped';

    public function getLabel(): string
    {
        return match ($this) {
            self::PLANNED => 'Đang lên kế hoạch xuất kho',
            self::RECEIVED => 'Đã nhận',
            self::CHECKED_IN => 'Đang kiểm tra nhập kho',
            self::STORED => 'Đã lưu kho',
            self::SHIPPED => 'Đã xuất kho',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PLANNED => 'gray',
            self::RECEIVED => 'warning',
            self::CHECKED_IN => 'info',
            self::STORED => 'success',
            self::SHIPPED => 'primary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PLANNED => 'heroicon-m-clock',
            self::RECEIVED => 'heroicon-m-inbox-arrow-down',
            self::CHECKED_IN => 'heroicon-m-check-badge',
            self::STORED => 'heroicon-m-archive-box',
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
