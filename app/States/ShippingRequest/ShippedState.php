<?php

namespace App\States\ShippingRequest;
use App\States\ShippingRequestState;

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