# State machine for Laravel 5

This state machine was adapted from multiple existing state machines by Hyn and Sebdesign's Laravel wrapper for the Winzou State machine.

It is following Hyn's approach to use classes for configuration of States and Transitions.

tbc...


## Graphical debugging

It has a command for printing the definition in [Dot](https://www.graphviz.org/doc/info/lang.html) format, e.g.

    artisan macgriog:fsm-debug "FQCN\Of\YourMachineDefinition" | dot -Tpng | display

## References

- https://github.com/hyn/state-machine
- https://github.com/sebdesign/laravel-state-machine
- https://github.com/winzou/state-machine
