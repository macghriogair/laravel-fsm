<?php

namespace Macgriog\StateMachine;

use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;
use Macgriog\StateMachine\Contracts\StateContract;

abstract class State implements StateContract
{
    use Named;

    protected $model;

    public function __construct(ProcessedByStateMachine $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function isInitial() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinal() : bool
    {
        return false;
    }
}
