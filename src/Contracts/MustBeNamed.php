<?php

namespace Macgriog\StateMachine\Contracts;

interface MustBeNamed
{
    /**
     * Returns a unique string.
     *
     * @return string
     */
    public function name() : string;
}
