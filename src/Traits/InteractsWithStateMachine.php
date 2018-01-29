<?php

namespace Macgriog\StateMachine\Traits;

use Macgriog\StateMachine\Contracts\FactoryContract;
use Macgriog\StateMachine\Contracts\MachineDefinitionContract;
use Macgriog\StateMachine\StateMachineException;

trait InteractsWithStateMachine
{
    public function applyTransition($transitionClass) : bool
    {
        if (! class_exists($transitionClass)) {
            return false;
        }

        $transition = new $transitionClass($this);

        $sm = $this->smFactory()->get($this, $this->smDefinition());

        try {
            return $sm->apply($transition);
        } catch (StateMachineException $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }

    public function currentState()
    {
        return $this->smFactory()
            ->get($this, $this->smDefinition())
            ->current();
    }

    public function nextTransitions()
    {
        return $this->smFactory()
            ->get($this, $this->smDefinition())
            ->nextTransitions();
    }

    public function nextSuggestedStates() : array
    {
        $suggests = [];

        $this->nextTransitions()
            ->map(function ($t) use (&$suggests) {
                $suggests = array_merge(array_map(function ($s) {
                    return (new $s($this))->name();
                }, $t->suggests()), $suggests);
            });

        return $suggests;
    }

    abstract protected function smFactory() : FactoryContract;
    abstract protected function smDefinition() : MachineDefinitionContract;
}
