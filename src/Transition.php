<?php

namespace Macgriog\StateMachine;

use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;
use Macgriog\StateMachine\Contracts\TransitionContract;

abstract class Transition implements TransitionContract
{
    use Named;

    protected $model;

    public function __construct(ProcessedByStateMachine $model)
    {
        $this->model = $model;
    }
}
