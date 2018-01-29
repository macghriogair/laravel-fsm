<?php

namespace Macgriog\StateMachine\Contracts;

interface MachineDefinitionContract
{
    /**
     * Models assigned to this state machine definition.
     *
     * @return array
     */
    public function models() : array;

    /**
     * @return array
     */
    public function mapping() : array;
}
