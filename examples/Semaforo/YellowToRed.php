<?php

namespace Examples\Semaforo;

use Macgriog\StateMachine\Contracts\StateContract;
use Macgriog\StateMachine\Contracts\TransitionContract;
use Macgriog\StateMachine\Transition;

class YellowToRed extends Transition implements TransitionContract
{

    /**
     * Whether the requirements are met to process through this transition.
     *
     * @return bool
     */
    public function areRequirementsMet(): bool
    {
        return true;
    }

    /**
     * Returns the list of suggested states to move into.
     *
     * @return array
     */
    public function suggests(): array
    {
        return [
            Red::class
        ];
    }

    /**
     * Processing the transition.
     *
     * @return StateContract
     */
    public function fire(): StateContract
    {
        return new Red($this->model);
    }
}
