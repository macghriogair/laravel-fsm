<?php

namespace Macgriog\StateMachine;

use Macgriog\StateMachine\Contracts\MachineDefinitionContract;
use Macgriog\StateMachine\Contracts\ProcessedByStateMachine;
use Macgriog\StateMachine\Contracts\StateContract;
use Macgriog\StateMachine\Contracts\StateMachineContract;
use Macgriog\StateMachine\Contracts\TransitionContract;
use Macgriog\StateMachine\Events\TransitionEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Implementing a Mealy machine.
 */
class StateMachine implements StateMachineContract
{
    protected $model;
    protected $definition;
    protected $dispatcher;
    protected $direction;
    protected $stateProperty;

    public function __construct(
        ProcessedByStateMachine $model,
        MachineDefinitionContract $definition,
        Dispatcher $dispatcher = null
    ) {
        $this->model = $model;
        $this->definition = $definition;
        $this->dispatcher = $dispatcher;

        $this->stateProperty = $model->stateProperty();

        try {
            $state = $this->getState();
            if (empty($state)) {
                $this->setState('state.' . $this->resolveStateByPosition('initial')->name());
            }
        } catch (NoSuchPropertyException $e) {
            throw new StateMachineException(
                sprintf(
                    "Cannot access configured property %s on model %s",
                    $this->stateProperty,
                    get_class($model)
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function current(): StateContract
    {
        $state = $this->getState();
        return $state ? $this->resolveByName($state) : $this->resolveStateByPosition('initial');
    }

    /**
     * @inheritdoc
     */
    public function nextTransitions($ignoreDirection = false): Collection
    {
        $state = $this->current();
        return $this->transitionInstances($state, true, $ignoreDirection);
    }

    /**
     * @inheritdoc
     */
    public function nextValidTransitions($ignoreDirection = false): Collection
    {
        $state = $this->current();
        return $this->transitionInstances($state, null, $ignoreDirection);
    }

    /**
     * @inheritdoc
     */
    public function can(TransitionContract $transition): bool
    {
        $state = $this->current();
        return $this->transitionInstances($state)->has($transition->name());
    }

    /**
     * @inheritdoc
     */
    public function apply(TransitionContract $transition): bool
    {
        if (!$this->can($transition)) {
            throw new StateMachineException(
                sprintf(
                    "Invalid transition %s for model %s with definition %s!",
                    $transition->name(),
                    json_encode($this->model),
                    get_class($this->definition)
                )
            );
        }

        if (!$transition->areRequirementsMet()) {
            return false;
        }

        $newState = $transition->fire();

        $event = new TransitionEvent($this->model, [
            'transition' => $transition->name(),
            'from' => $this->current()->name(),
            'to' => $newState->name(),
        ]);

        $success = $this->saveModelState("state.{$newState->name()}");
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch($event);
        }

        return $success;
    }

    /**
     * @inheritdoc
     */
    public function forward(): bool
    {
        $transition = $this->identifyNextTransition();

        if ($transition) {
            return $this->apply($transition);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isInitialState(): bool
    {
        return $this->current()->isInitial();
    }

    /**
     * @inheritdoc
     */
    public function isFinalState(): bool
    {
        return $this->current()->isFinal();
    }

    /**
     * @inheritdoc
     */
    public function getObject(): ProcessedByStateMachine
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): MachineDefinitionContract
    {
        return $this->definition;
    }

    protected function identifyNextTransition(StateContract $nextState = null)
    {
        $currentState = $this->current();
        $requirementsMet = new Collection();

        /** @var TransitionContract $transition */
        foreach ($this->transitionInstances($currentState) as $transition) {
            if (!$nextState || ($nextState && !in_array($nextState, $transition->suggests()))) {
                if (!$transition->areRequirementsMet()) {
                    continue;
                }

                $requirementsMet->put($transition->name(), $transition);
            }
        }

        return $requirementsMet->first();
    }

    protected function getState()
    {
        $accessor = new PropertyAccessor();
        return $accessor->getValue($this->model, $this->stateProperty);
    }

    protected function setState($state): self
    {
        $accessor = new PropertyAccessor();
        $accessor->setValue($this->model, $this->stateProperty, $state);
        return $this;
    }

    protected function saveModelState($state): bool
    {
        $saved = false;
        $this->setState($state);
        if ($this->model->isDirty($this->stateProperty)
            && $this->model->exists) {
            $saved = $this->model->save();
        }
        return $saved;
    }

    protected function getNextState(string $transition)
    {
        $currentState = $this->getState();
        return $this->definition->mapping()[$currentState][$transition];
    }

    protected function transitionInstances(
        StateContract $state = null,
        $ignoreValidation = false,
        $ignoreDirection = true
    ) {
        $collection = new Collection();

        if ($state) {
            $transitions = $this->fetchStateTransitions($state, $ignoreDirection);
            foreach ($transitions as $transitionClass) {
                if (!class_exists($transitionClass)) {
                    throw new StateMachineException(
                        "$transitionClass not found" .
                        ($state ? " while in state " . get_class($state) : '')
                    );
                }

                /** @var TransitionContract $instance */
                $instance = new $transitionClass($this->model);
                if (!$ignoreValidation && !$instance->areRequirementsMet()) {
                    continue;
                }
                $collection->put($instance->name(), $instance);
            }
        }

        return $collection;
    }

    protected function fetchStateTransitions(StateContract $state, $ignoreDirection = true): array
    {
        if ($ignoreDirection || empty($this->direction)) {
            return array_flatten($state->transitions());
        }

        return isset($state->transitions()[$this->direction]) ? $state->transitions()[$this->direction] : [];
    }

    public function resolveByName($state = '')
    {
        if (empty($state) || !strstr($state, '.')) {
            throw new StateMachineException($state);
        }

        list($type, $identifier) = explode('.', $state);

        $type = Str::plural($type);

        foreach (Arr::get($this->definition->mapping(), $type, []) as $state) {
            /** @var State $instance */
            $instance = new $state($this->model);

            if ($instance->name() == $identifier) {
                return $instance;
            }
        }
    }

    public function setDirection(string $direction)
    {
        $this->direction = $direction;

        return $this;
    }

    protected function resolveStateByPosition($position = 'initial')
    {
        $method = Str::camel("is_{$position}");
        $found = new Collection();
        foreach (Arr::get($this->definition->mapping(), 'states', []) as $state) {
            $instance = new $state($this->model);

            if (method_exists($instance, $method) && $instance->{$method}() === true) {
                $found->put($instance->name(), $instance);
            }
        }

        return $found->count() > 1 ? $found : $found->first();
    }
}
