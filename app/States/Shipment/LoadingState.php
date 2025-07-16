<?php

namespace App\States\Shipment;
use App\States\ShipmentState;

class LoadingState extends ShipmentState
{
    public static $name = 'loading';

    public function color(): string
    {
        return 'warning';
    }

    public function label(): string
    {
        return 'Chuẩn bị hàng';
    }

    public function icon(): string
    {
        return 'heroicon-o-arrow-down-on-square-stack';
    }
    
    public function badgeClass(): string
    {
        return 'bg-yellow-100 text-yellow-800';
    }
}