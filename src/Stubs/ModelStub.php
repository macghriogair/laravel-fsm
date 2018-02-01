<?php

namespace Macgriog\StateMachine\Stubs;

use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;

class ModelStub implements ProcessedByStateMachine
{
    public $state;

    public function stateProperty(): string
    {
        return 'state';
    }
}
