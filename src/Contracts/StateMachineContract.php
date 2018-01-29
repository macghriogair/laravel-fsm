<?php

namespace Macgriog\StateMachine\Contracts;

use Illuminate\Support\Collection;

interface StateMachineContract
{
    /**
     * Get the current state.
     *
     * @return StateContract|int
     */
    public function current(): StateContract;

    /**
     * Get the next transitions for the current state.
     *
     * @return Collection
     */
    public function nextTransitions($ignoreDirection = false): Collection;

    /**
     * Get the next validated transitions for the current state.
     *
     * @return Collection
     */
    public function nextValidTransitions($ignoreDirection = false): Collection;

    /**
     * Determine whether a transition can be applied.
     *
     * @param TransitionContract $transition
     * @return bool
     */
    public function can(TransitionContract $transition): bool;

    /**
     * Apply a transition.
     *
     * @param TransitionContract $transition
     * @return bool
     */
    public function apply(TransitionContract $transition): bool;

    /**
     * Identify the next transition and apply it.
     *
     * @return bool
     */
    public function forward(): bool;

    /**
     * Whether the current state is the first state.
     *
     * @return bool
     */
    public function isInitialState(): bool;

    /**
     * Whether the current state is a final state.
     *
     * @return bool
     */
    public function isFinalState(): bool;

    /**
     * Returns the underlying model instance.
     *
     * @return ProcessedByStateMachine
     */
    public function getObject(): ProcessedByStateMachine;

    /**
     * Returns the underlying machine definition.
     *
     * @return MachineDefinitionContract
     */
    public function getDefinition(): MachineDefinitionContract;

    /**
     * Sets a direction to select transitions.
     *
     * @return  self
     */
    public function setDirection(string $direction);
}
