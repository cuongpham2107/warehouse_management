<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;
use App\States\Shipment\LoadingState;
use App\States\Shipment\ReadyState;
use App\States\Shipment\DepartedState;
use App\States\Shipment\DeliveredState;
use App\States\Shipment\ReturnedState;

abstract class ShipmentState extends State
{
    abstract public function color(): string;
    abstract public function label(): string;
    abstract public function icon(): string;
    abstract public function badgeClass(): string;
    
    /**
     * Trả về mảng [class => label] cho tất cả các trạng thái
     */
    public static function getStateOptions(): array
    {
        return [
            'loading' => (new LoadingState('loading'))->label(),
            'ready' => (new ReadyState('ready'))->label(),
            'departed' => (new DepartedState('departed'))->label(),
            'delivered' => (new DeliveredState('delivered'))->label(),
            'returned' => (new ReturnedState('returned'))->label(),
        ];
    }

    /**
     * Trả về class State tương ứng với key
     */
    public static function getStateClass(string $key): string
    {
        return match($key) {
            'loading' => LoadingState::class,
            'ready' => ReadyState::class,
            'departed' => DepartedState::class,
            'delivered' => DeliveredState::class,
            'returned' => ReturnedState::class,
            default => LoadingState::class,
        };
    }
}