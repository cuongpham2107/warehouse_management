<?php

namespace App\States\Shipment;
use App\States\ShipmentState;

class ReturnedState extends ShipmentState
{
    public static $name = 'returned';

    public function color(): string
    {
        return 'danger';
    }

    public function label(): string
    {
        return 'Đã trả về';
    }

    public function icon(): string
    {
        return 'heroicon-o-arrow-uturn-left';
    }
    
    public function badgeClass(): string
    {
        return 'bg-red-100 text-red-800';
    }
}