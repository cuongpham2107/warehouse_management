<?php

namespace App\States;

class ShippedState extends ShippingRequestState
{
    public static $name = 'shipped';

    public function color(): string
    {
        return 'success';
    }

    public function label(): string
    {
        return 'Đã vận chuyển';
    }

    public function icon(): string
    {
        return 'heroicon-m-truck';
    }
}