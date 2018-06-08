<?php

namespace A3020\SpeedAnalyzer\Provider;

use A3020\SpeedAnalyzer\Client;
use A3020\SpeedAnalyzer\Request\Tracker;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;

class ClientServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Repository */
    protected $config;

    /** @var Client */
    private $client;

    public function __construct(Repository $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    public function register()
    {
        $this->app->singleton('speed_analyzer_tracker', Tracker::class);

        $this->overrideDispatcher();
        $this->registerEventListeners();
    }

    /**
     * By overriding the dispatcher we're actually catching ALL events
     *
     * This results in better reporting, but because your application
     * may already override the dispatcher, we've made it configurable.
     */
    private function overrideDispatcher()
    {
        if (!$this->shouldOverrideDispatcher()) {
            return;
        }

        $provider = $this->app->make(EventServiceProvider::class);
        $provider->register();
    }

    /**
     * Manually register events to listen to
     *
     * If the event dispatcher is not overridden, we should manually
     * add the event listeners. They consist of custom events from
     * the config, and a hard-coded list of events from the core.
     */
    private function registerEventListeners()
    {
        if ($this->shouldOverrideDispatcher()) {
            return;
        }

        $this->addListeners(
            (array) $this->config->get('speed_analyzer.reports.custom_events', [])
        );
    }

    /**
     * Return true if the event dispatcher should be overridden
     *
     * @return bool
     */
    private function shouldOverrideDispatcher()
    {
        return (bool) $this->config->get('speed_analyzer.reports.override_event_dispatcher', false) === true;
    }

    /**
     * Manually register events to listen to
     *
     * There is a built-in list which is merged with
     * custom events from the config (if present).
     *
     * @param array $customEvents
     */
    private function addListeners(array $customEvents)
    {
        $defaultEvents = [
            'on_speed_analyzer_started',
            'on_speed_analyzer_track',
            'on_before_dispatch',
            'on_start',
            'on_before_render',
            'on_render_complete',
            'on_locale_load',
            'on_shutdown',
            'on_page_output',
            'on_page_view',
            'on_block_load',
            'on_block_before_render',
            'on_shutdown',
        ];

        $events = array_merge($defaultEvents, $customEvents);
        $events = array_unique($events);

        foreach ($events as $eventName) {
            $this->app['director']->addListener($eventName, function ($event) use ($eventName) {
                $this->app->make(\A3020\SpeedAnalyzer\Listener\Track::class)->handle($eventName, $event);
            });
        }
    }
}
