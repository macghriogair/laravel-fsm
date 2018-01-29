<?php

namespace Macgriog\StateMachine;

use Macgriog\StateMachine\Contracts\FactoryContract;
use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return FactoryContract::class;
    }
}
