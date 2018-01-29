<?php

namespace Macgriog\StateMachine\Events;

use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransitionEvent
{
    use Dispatchable, SerializesModels;

    public $model;

    public $data;

    /**
     * Create a new event instance.
     *
     * @param ProcessedByStateMachine $model
     * @param $data
     */
    public function __construct(ProcessedByStateMachine $model, $data)
    {
        $this->model = $model;
        $this->data = $data;
    }
}
