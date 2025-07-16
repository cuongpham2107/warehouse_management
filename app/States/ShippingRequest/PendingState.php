<?php

namespace App\States\ShippingRequest;
use App\States\ShippingRequestState;

class PendingState extends ShippingRequestState
{
    public static $name = 'pending';

    public function color(): string
    {
        return 'warning';
    }

    public function label(): string
    {
        return 'Chờ xử lý';
    }

    public function icon(): string
    {
        return 'heroicon-m-clock';
    }
}