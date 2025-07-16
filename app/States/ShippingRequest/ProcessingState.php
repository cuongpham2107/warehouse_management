<?php

namespace App\States\ShippingRequest;
use App\States\ShippingRequestState;

class ProcessingState extends ShippingRequestState
{
    public static $name = 'processing';

    public function color(): string
    {
        return 'info';
    }

    public function label(): string
    {
        return 'Đang xử lý';
    }

    public function icon(): string
    {
        return 'heroicon-m-cog';
    }
}