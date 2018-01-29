<?php

namespace Macgriog\StateMachine\Contracts;

interface FactoryContract
{
    /**
     * Returns the state machine matching the couple model / definition
     *
     * @param ProcessedByStateMachine $model
     * @param MachineDefinitionContract $definition
     *
     * @return StateMachineContract
     */
    public function get(ProcessedByStateMachine $model, MachineDefinitionContract $definition);
}
