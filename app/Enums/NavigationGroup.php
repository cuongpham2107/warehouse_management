<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel, HasIcon
{
    case Goods;

    case Imports;

    public function getLabel(): string
    {
        return match ($this) {
            self::Goods => 'Hàng hóa',
            self::Imports => 'Nhập kho',

            
        };
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::Goods => 'heroicon-o-archive-box',
            self::Imports => 'heroicon-o-arrow-down-tray',
        };
    }
}