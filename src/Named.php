<?php

namespace Macgriog\StateMachine;

use Illuminate\Support\Str;

trait Named
{
    /**
     * @return string
     */
    public static function resolveName() : string
    {
        $class = str_replace(['\\'], '', get_called_class());
        return Str::snake($class, '-');
    }

    /**
     * Returns a unique string.
     *
     * @return string
     */
    public function name() : string
    {
        return call_user_func([get_called_class(), 'resolveName']);
    }
}
