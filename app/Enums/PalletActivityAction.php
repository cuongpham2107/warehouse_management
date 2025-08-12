<?php

namespace App\Enums;

enum PalletActivityAction: string
{
    
    case ATTACH_CRATE = 'attach_crate';
    case IMPORT_PALLET = 'import_pallet';
    case RELOCATE_PALLET = 'relocate_pallet';
    case EXPORT_PALLET = 'export_pallet';

    public function getLabel(): string
    {
        return match ($this) {
            self::ATTACH_CRATE => 'Gắn crate vào pallet',
            self::IMPORT_PALLET => 'Nhập kho',
            self::RELOCATE_PALLET => 'Chuyển vị trí pallet sang vị trí khác',
            self::EXPORT_PALLET => 'Xuất kho',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ATTACH_CRATE => 'info',
            self::IMPORT_PALLET => 'primary',
            self::RELOCATE_PALLET => 'warning',
            self::EXPORT_PALLET => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ATTACH_CRATE => 'heroicon-m-plus-circle',
            self::IMPORT_PALLET => 'heroicon-m-arrows-up-down',
            self::RELOCATE_PALLET => 'heroicon-m-arrows-right-left',
            self::EXPORT_PALLET => 'heroicon-m-trash',
        };
    }
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
