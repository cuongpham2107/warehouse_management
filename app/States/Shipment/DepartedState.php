<?php

namespace App\States\Shipment;
use App\States\ShipmentState;

class DepartedState extends ShipmentState
{
    public static $name = 'departed';

    public function color(): string
    {
        return 'primary';
    }

    public function label(): string
    {
        return 'Đã khởi hành';
    }

    public function icon(): string
    {
        return 'heroicon-o-truck';
    }
    
    public function badgeClass(): string
    {
        return 'bg-indigo-100 text-indigo-800';
    }
}