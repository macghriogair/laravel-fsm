<?php

namespace Examples\Semaforo;

use Illuminate\Database\Eloquent\Model;
use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;

class TrafficLight extends Model implements ProcessedByStateMachine
{
    public function stateProperty(): string
    {
        return 'state';
    }
}
