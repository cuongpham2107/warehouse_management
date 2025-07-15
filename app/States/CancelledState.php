<?php

namespace App\States;

class CancelledState extends ShippingRequestState
{
    public static $name = 'cancelled';

    public function color(): string
    {
        return 'danger';
    }

    public function label(): string
    {
        return 'Đã hủy';
    }

    public function icon(): string
    {
        return 'heroicon-m-x-circle';
    }
}