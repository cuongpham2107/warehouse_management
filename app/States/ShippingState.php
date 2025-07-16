<?php

namespace App\States;

class ShippingState extends ShippingRequestState
{
    public static $name = 'shipping';

    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Đang vận chuyển';
    }

    public function icon(): string
    {
        return 'heroicon-m-truck';
    }
}