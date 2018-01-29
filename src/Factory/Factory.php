<?php

namespace Macgriog\StateMachine\Factory;

use Macgriog\StateMachine\Contracts\FactoryContract;
use Macgriog\StateMachine\Contracts\MachineDefinitionContract;
use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;
use Macgriog\StateMachine\StateMachine;
use Macgriog\StateMachine\StateMachineException;
use Illuminate\Contracts\Events\Dispatcher;

class Factory implements FactoryContract
{
    protected $machines = [];

    protected $dispatcher;

    public function __construct(
        Dispatcher $dispatcher = null
    ) {
        $this->dispatcher = $dispatcher;
    }

    public function get(ProcessedByStateMachine $model, MachineDefinitionContract $definition)
    {
        $hash = spl_object_hash($model);
        $definitionClass = get_class($definition);

        if (isset($this->machines[$hash][$definitionClass])) {
            return $this->machines[$hash][$definitionClass];
        }

        $modelClass = get_class($model);
        if (! in_array($modelClass, $definition->models())) {
            throw new StateMachineException(
                sprintf(
                    "Can't create state machine for %s which is not supported by definition %s!",
                    $modelClass,
                    $definitionClass
                )
            );
        }
        return $this->createStateMachine($model, $definition);
    }

    protected function createStateMachine($model, $definition)
    {
        return new StateMachine($model, $definition, $this->dispatcher);
    }
}
