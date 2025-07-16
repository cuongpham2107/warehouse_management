<?php

namespace App\States\ShippingRequest;
use App\States\ShippingRequestState;

class ReadyState extends ShippingRequestState
{
    public static $name = 'ready';

    public function color(): string
    {
        return 'primary';
    }

    public function label(): string
    {
        return 'Sẵn sàng';
    }

    public function icon(): string
    {
        return 'heroicon-m-check-circle';
    }
}