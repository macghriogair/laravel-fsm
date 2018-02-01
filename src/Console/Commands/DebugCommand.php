<?php

/**
 * @date    2018-02-01
 * @file    DebugCommand.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

namespace Macgriog\StateMachine\Commands;

use Macgriog\StateMachine\Contracts\MachineDefinitionContract;
use Macgriog\StateMachine\Contracts\StateMachineContract;
use Macgriog\StateMachine\StateMachine;
use Macgriog\StateMachine\Stubs\ModelStub;
use Illuminate\Console\Command;

class DebugCommand extends Command
{
    private $modelStub;
    private $buffer;
    private $seen;
    private $color;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macgriog:fsm-debug
        {fsm : The FQCN of the Machine Definition Contract.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate graphical output for Machine Definition Contract.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->modelStub = new ModelStub();
        $this->buffer = [];
        $this->seen = [];
        $this->color = 'dodgerblue'; // TODO
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $machineDefClass = $this->argument('fsm');
        if (! class_exists($machineDefClass)) {
            throw new \InvalidArgumentException("Class {$machineDefClass} not found.");
        }

        $sm = $this->stateMachine(new $machineDefClass());

        $this->buffer[] = 'digraph G {';

        $initialState = $sm->current()->name();
        $this->visitMachine($sm, $initialState);

        $this->buffer[] = '}';

        print join(PHP_EOL, $this->buffer) . PHP_EOL;
    }

    protected function visitMachine(StateMachineContract $sm, string $currentState)
    {
        if (in_array($currentState, $this->seen)) {
            return;
        }

        $this->seen[] = $currentState;
        $this->modelStub->state = 'state.' . $currentState;

        $transitions = $sm->nextTransitions();

        foreach ($transitions as $t) {
            $this->seen[$currentState][] = $t->name();
            foreach ($t->suggests() as $next) {
                $nextInstance = new $next($this->modelStub);
                $nextState = $nextInstance->name();

                $this->buffer[] = sprintf(
                    '  "%s" -> "%s" [color=%s];',
                    $currentState,
                    $nextState,
                    $this->color
                );

                $this->visitMachine($sm, $nextState);
            }
        }
    }

    protected function stateMachine(MachineDefinitionContract $machineDef) : StateMachineContract
    {
        return new StateMachine($this->modelStub, $machineDef);
    }
}
