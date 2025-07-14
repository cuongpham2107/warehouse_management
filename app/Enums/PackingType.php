<?php

namespace App\Enums;

enum PackingType: string
{
    case STANDARD = 'standard';
    case BOX = 'box';
    case PALLET = 'pallet';
    case CRATE = 'crate';

    public function getLabel(): string
    {
        return match ($this) {
            self::STANDARD => 'Tiêu chuẩn',
            self::BOX => 'Hộp',
            self::PALLET => 'Pallet',
            self::CRATE => 'Thùng',
        };
    }
    public function getDescription(): string
    {
        return match ($this) {
            self::STANDARD => 'Đóng gói tiêu chuẩn',
            self::BOX => 'Đóng gói trong hộp',
            self::PALLET => 'Đóng gói trên pallet',
            self::CRATE => 'Đóng gói trong thùng',
        };
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::STANDARD => 'heroicon-o-cube',
            self::BOX => 'heroicon-o-archive-box',
            self::PALLET => 'heroicon-o-rectangle-stack',
            self::CRATE => 'heroicon-o-cube',
        };
    }
    public function getColor(): string
    {
        return match ($this) {
            self::STANDARD => 'primary',
            self::BOX => 'info',
            self::PALLET => 'success',
            self::CRATE => 'warning',
            default => 'gray',
        };
    }
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
