<?php

namespace App\Enums;

enum ShippingRequestPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => 'Thấp',
            self::MEDIUM => 'Trung bình',
            self::HIGH => 'Cao',
            self::URGENT => 'Khẩn cấp',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LOW => 'success',
            self::MEDIUM => 'warning',
            self::HIGH => 'danger',
            self::URGENT => 'danger',
        };
    }
    public function getIcon(): string
    {
        return match ($this) {
            self::LOW => 'heroicon-m-arrow-down',
            self::MEDIUM => 'heroicon-m-arrow-right',
            self::HIGH => 'heroicon-m-arrow-up',
            self::URGENT => 'heroicon-m-exclamation',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::LOW => 'bg-green-100 text-green-800',
            self::MEDIUM => 'bg-yellow-100 text-yellow-800',
            self::HIGH => 'bg-orange-100 text-orange-800',
            self::URGENT => 'bg-red-100 text-red-800',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
