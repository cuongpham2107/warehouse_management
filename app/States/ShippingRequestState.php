<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ShippingRequestState extends State
{

    abstract public function color(): string;
    abstract public function label(): string;
    abstract public function icon(): string;
    /**
     * Trả về mảng [class => label] cho tất cả các trạng thái
     */
    public static function getStateOptions(): array
    {
        return [
            'pending' => (new PendingState('pending'))->label(),
            'processing' => (new ProcessingState('processing'))->label(),
            'ready' => (new ReadyState('ready'))->label(),
            'shipped' => (new ShippingState('shipped'))->label(),
            'delivered' => (new DeliveredState('delivered'))->label(),
            'cancelled' => (new CancelledState('cancelled'))->label(),
        ];
    }

    /**
     * Trả về class State tương ứng với key
     */
    public static function getStateClass(string $key): string
    {
        return match($key) {
            'pending' => PendingState::class,
            'processing' => ProcessingState::class,
            'ready' => ReadyState::class,
            'shipped' => ShippingState::class,
            'delivered' => DeliveredState::class,
            'cancelled' => CancelledState::class,
            default => PendingState::class,
        };
    }

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(PendingState::class)
            ->allowTransition(PendingState::class, ProcessingState::class)
            ->allowTransition(PendingState::class, CancelledState::class)
            ->allowTransition(ProcessingState::class, ReadyState::class)
            ->allowTransition(ProcessingState::class, CancelledState::class)
            ->allowTransition(ReadyState::class, CancelledState::class);
    }
}