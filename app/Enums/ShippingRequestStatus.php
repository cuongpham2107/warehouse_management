<?php

namespace App\Enums;

enum ShippingRequestStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case READY = 'ready';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ xử lý',
            self::PROCESSING => 'Đang xử lý',
            self::READY => 'Sẵn sàng',
            self::SHIPPED => 'Đã vận chuyển',
            self::DELIVERED => 'Đã giao hàng',
            self::CANCELLED => 'Đã hủy',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::READY => 'primary',
            self::SHIPPED => 'success',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::PROCESSING => 'bg-blue-100 text-blue-800',
            self::READY => 'bg-indigo-100 text-indigo-800',
            self::SHIPPED => 'bg-purple-100 text-purple-800',
            self::DELIVERED => 'bg-green-100 text-green-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
