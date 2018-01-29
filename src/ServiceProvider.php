<?php

namespace Macgriog\StateMachine;

use Macgriog\StateMachine\Contracts\FactoryContract;
use Macgriog\StateMachine\Factory\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerFactory();
    }

    protected function registerFactory()
    {
        $this->app->singleton(FactoryContract::class, function () {
            return new Factory(
                $this->app->make(Dispatcher::class)
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            FactoryContract::class
        ];
    }
}
