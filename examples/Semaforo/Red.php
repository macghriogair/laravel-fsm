<?php

namespace Examples\Semaforo;

use Macgriog\StateMachine\Contracts\StateContract;
use Macgriog\StateMachine\State;

class Red extends State implements StateContract
{
    public function name(): string
    {
        return 'Red';
    }

    /**
     * Returns all possible transitions from this state.
     *
     * @return array
     */
    public function transitions(): array
    {
        return [
            RedToGreen::class
        ];
    }
}
