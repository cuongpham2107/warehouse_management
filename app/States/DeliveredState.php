<?php

namespace App\States;

class DeliveredState extends ShippingRequestState
{
    public static $name = 'delivered';

    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Đã giao hàng';
    }

    public function icon(): string
    {
        return 'heroicon-m-check-circle';
    }
}