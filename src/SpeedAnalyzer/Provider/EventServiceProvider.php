<?php

namespace A3020\SpeedAnalyzer\Provider;

use A3020\SpeedAnalyzer\Event\EventDispatcher;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Support\Facade\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Set the singleton on the dispatcher implementation
        $this->app->singleton(EventDispatcher::class);

        // Bind the interface to the implementation
        $this->app->bind(EventDispatcherInterface::class, EventDispatcher::class);

        // Add the 'director' alias in a backwards compatible way.
        $this->app->bind('director', EventDispatcherInterface::class);

        Events::clearResolvedInstance('director');
    }
}
