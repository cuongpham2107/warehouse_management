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
            self::PLANNED => 'Đã lên kế hoạch',
            self::RECEIVED => 'Đã nhận',
            self::CHECKED_IN => 'Đã kiểm tra nhập kho',
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

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
