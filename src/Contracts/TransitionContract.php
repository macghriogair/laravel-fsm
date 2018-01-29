<?php

namespace Macgriog\StateMachine\Contracts;

interface TransitionContract extends MustBeNamed
{
    /**
     * Whether the requirements are met to process through this transition.
     *
     * @return bool
     */
    public function areRequirementsMet() : bool;

    /**
     * Returns the list of suggested states to move into.
     *
     * @return array
     */
    public function suggests() : array;

    /**
     * Processing the transition.
     *
     * @return StateContract
     */
    public function fire() : StateContract;
}
