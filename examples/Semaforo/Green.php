<?php

namespace Examples\Semaforo;

use Macgriog\StateMachine\Contracts\StateContract;
use Macgriog\StateMachine\State;

class Green extends State implements StateContract
{
    public function name(): string
    {
        return 'Green';
    }

    public function isInitial(): bool
    {
        return true;
    }

    /**
     * Returns all possible transitions from this state.
     *
     * @return array
     */
    public function transitions(): array
    {
        return [
            GreenToYellow::class
        ];
    }
}
