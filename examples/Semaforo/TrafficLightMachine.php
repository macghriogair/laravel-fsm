<?php

namespace Examples\Semaforo;

use Macgriog\StateMachine\Contracts\MachineDefinitionContract;

class TrafficLightMachine implements MachineDefinitionContract
{
    /**
     * Models assigned to this state machine definition.
     *
     * @return array
     */
    public function models(): array
    {
        return [
            TrafficLight::class
        ];
    }

    /**
     * @return array
     */
    public function mapping(): array
    {
        return [
            'states' => [
                Green::class,
                Yellow::class,
                Red::class
            ],
            'transitions' => [
                GreenToYellow::class,
                YellowToRed::class,
                RedToGreen::class
            ]
        ];
    }
}
