<?php

namespace A3020\SpeedAnalyzer\Provider;

use A3020\SpeedAnalyzer\Request\Tracker;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;

class SpeedAnalyzerServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function register()
    {
        $this->registerRoutes();

        if (!$this->shouldTrack()) {
            return;
        }

        $this->logSqlQueries();

        $this->app->singleton('speed_analyzer_tracker', Tracker::class);

        if ((bool)$this->config->get('speed_analyzer.reports.override_event_dispatcher', false) === true) {
            $this->overrideDispatcher();
        } else {
            $this->listeners();
        }

        $this->app['director']->addListener('on_user_delete', function($event) {
            // Delete Reports if a user is deleted. #gdpr
            $this->app->make(\A3020\SpeedAnalyzer\Listener\OnUserDelete\DeleteReports::class)
                ->handle($event);
        });

        $this->app['director']->dispatch('on_speed_analyzer_started');
    }

    private function registerRoutes()
    {
        /** @var RouterInterface $router */
        $router = $this->app->make(RouterInterface::class);

        $router->registerMultiple([
            '/ccm/system/speed_analyzer/reports' => [
                '\A3020\SpeedAnalyzer\Ajax\Reports::getPage',
            ],
            '/ccm/system/speed_analyzer/query/{eventId}' => [
                '\A3020\SpeedAnalyzer\Ajax\QueryDetails::view',
            ],
            'ccm/system/speed_optimizer/diagnosis/environment' => [
                '\A3020\SpeedAnalyzer\Ajax\Diagnosis\Environment::view',
            ],
            'ccm/system/speed_optimizer/diagnosis/location' => [
                '\A3020\SpeedAnalyzer\Ajax\Diagnosis\Location::view',
            ],
            'ccm/system/speed_optimizer/diagnosis/packages' => [
                '\A3020\SpeedAnalyzer\Ajax\Diagnosis\Packages::view',
            ],
        ]);
    }

    private function overrideDispatcher()
    {
        $provider = $this->app->make(EventServiceProvider::class);
        $provider->register();
    }

    private function logSqlQueries()
    {
        if ((bool) $this->config->get('speed_analyzer.reports.log_sql_queries', true)) {
            $stack = new \Doctrine\DBAL\Logging\DebugStack();

            /** @var EntityManager $em */
            $em = $this->app->make(EntityManager::class);
            $em->getConnection()->getConfiguration()->setSQLLogger($stack);
        }
    }

    private function listeners()
    {
        if (!$this->shouldTrack()) {
            return;
        }

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

        $events = array_merge($defaultEvents, (array) $this->config->get('speed_analyzer.reports.custom_events', []));
        $events = array_unique($events);

        foreach ($events as $eventName) {
            $this->app['director']->addListener($eventName, function ($event) use ($eventName) {
                $this->app->make(\A3020\SpeedAnalyzer\Listener\Track::class)->handle($eventName, $event);
            });
        }
    }

    private function shouldTrack()
    {
        // If Speed Analyzer is disabled, do not track anything
        if ((bool) $this->config->get('speed_analyzer.enabled', false) === false) {
            return false;
        }

        // We don't have access to the Page object yet
        // We also check for edit mode etc. when the Report is written,
        // but to save resources, we also do a quick check based on the URL parameters.
        /** @var Request $request */
        $request = $this->app->make(\Concrete\Core\Http\Request::class);

        // Do not track when a page is in edit mode (configurable)
        if ($this->config->get('speed_analyzer.reports.enabled_in_edit_mode', false) === false
            && stripos($request->getRequestUri(), 'ctask=check-out') !== false) {
            return false;
        }

        // Do not track the dashboard area (configurable)
        if ($this->config->get('speed_analyzer.reports.enabled_in_dashboard', false) === false
            && stripos($request->getRequestUri(), '/dashboard/') !== false) {
            return false;
        }

        // If we also track the dashboard, ok, but never the Speed Analyzer section
        if (stripos($request->getRequestUri(), '/dashboard/speed_analyzer/') !== false) {
            return false;
        }

        // It's possible to disable tracking AJAX requests.
        if ($request->isXmlHttpRequest() && (bool) $this->config->get('speed_analyzer.reports.track_ajax_requests', true) === false) {
            return false;
        }

        return true;
    }
}
