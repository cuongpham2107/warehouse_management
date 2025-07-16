<?php

namespace App\States\Shipment;
use App\States\ShipmentState;

class DeliveredState extends ShipmentState
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
        return 'heroicon-o-check-circle';
    }
    
    public function badgeClass(): string
    {
        return 'bg-green-100 text-green-800';
    }
}