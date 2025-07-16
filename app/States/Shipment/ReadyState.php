<?php

namespace App\States\Shipment;
use App\States\ShipmentState;

class ReadyState extends ShipmentState
{
    public static $name = 'ready';

    public function color(): string
    {
        return 'info';
    }

    public function label(): string
    {
        return 'Sẵn sàng';
    }

    public function icon(): string
    {
        return 'heroicon-o-check-badge';
    }
    
    public function badgeClass(): string
    {
        return 'bg-blue-100 text-blue-800';
    }
}