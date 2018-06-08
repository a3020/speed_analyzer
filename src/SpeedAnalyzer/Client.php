<?php

namespace A3020\SpeedAnalyzer;

use A3020\SpeedAnalyzer\Log\QueryLogger;
use A3020\SpeedAnalyzer\Provider\ClientServiceProvider;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;

class Client implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var ClientServiceProvider
     */
    private $provider;

    /**
     * @var Repository
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    public function __construct(ClientServiceProvider $provider, Repository $config, Request $request)
    {
        $this->provider = $provider;
        $this->config = $config;
        $this->request = $request;
    }

    public function start()
    {
        if (!$this->shouldTrack()) {
            return;
        }

        $this->provider->register();

        $this->logSqlQueries();
    }

    /**
     * Should Speed Analyzer track the current request?
     *
     * @return bool
     */
    private function shouldTrack()
    {
        // If Speed Analyzer is disabled, do not track anything
        if ((bool) $this->config->get('speed_analyzer.enabled', false) === false) {
            return false;
        }

        // We don't have access to the Page object yet
        // We also check for edit mode etc. when the Report is written,
        // but to save resources, we also do a quick check based on the URL parameters.

        // Do not track when a page is in edit mode (configurable)
        if ($this->config->get('speed_analyzer.reports.enabled_in_edit_mode', false) === false
            && stripos($this->request->getRequestUri(), 'ctask=check-out') !== false) {
            return false;
        }

        // Do not track the dashboard area (configurable)
        if ($this->config->get('speed_analyzer.reports.enabled_in_dashboard', false) === false
            && stripos($this->request->getRequestUri(), '/dashboard/') !== false) {
            return false;
        }

        // If we also track the dashboard, ok, but never the Speed Analyzer section
        if (stripos($this->request->getRequestUri(), '/dashboard/speed_analyzer/') !== false) {
            return false;
        }

        // It's possible to disable tracking AJAX requests.
        if ($this->request->isXmlHttpRequest() && (bool) $this->config->get('speed_analyzer.reports.track_ajax_requests', true) === false) {
            return false;
        }

        return true;
    }

    /**
     * Make sure database queries are stored if enabled
     */
    private function logSqlQueries()
    {
        if (!(bool) $this->config->get('speed_analyzer.reports.log_sql_queries', true)) {
            return;
        }

        $this->app->make(QueryLogger::class);
    }
}
