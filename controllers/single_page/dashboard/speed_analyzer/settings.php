<?php

namespace Concrete\Package\SpeedAnalyzer\Controller\SinglePage\Dashboard\SpeedAnalyzer;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class Settings extends DashboardPageController
{
    /** @var Repository $config */
    protected $config;

    public function view()
    {
        $this->config = $this->app->make(Repository::class);

        $this->set('isEnabled', (bool) $this->config->get('speed_analyzer.enabled', false));
        $this->set('enabledInEditMode', (bool) $this->config->get('speed_analyzer.reports.enabled_in_edit_mode', false));
        $this->set('enabledInDashboard', (bool) $this->config->get('speed_analyzer.reports.enabled_in_dashboard', false));
        $this->set('overrideEventDispatcher', (bool) $this->config->get('speed_analyzer.reports.override_event_dispatcher', false));
        $this->set('overwriteReports', (bool) $this->config->get('speed_analyzer.reports.overwrite_reports', false));
        $this->set('logSqlQueries', (bool) $this->config->get('speed_analyzer.reports.log_sql_queries', true));
        $this->set('trackAjaxRequests', (bool) $this->config->get('speed_analyzer.reports.track_ajax_requests', true));
        $this->set('customEvents', implode("\n", $this->config->get('speed_analyzer.reports.custom_events', [])));
        $this->set('writeIfExecTimeLongerThan', $this->config->get('speed_analyzer.reports.write_if_exec_time_longer_than'));

        $this->set('isProduction', $this->app->environment() === 'production');
    }

    public function save()
    {
        if (!$this->token->validate('a3020.speed_analyzer.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return Redirect::to('/dashboard/speed_analyzer/settings');
        }

        /** @var Repository $enableLog */
        $config = $this->app->make(Repository::class);

        $config->save('speed_analyzer.enabled', (bool) $this->post('isEnabled'));
        $config->save('speed_analyzer.reports.enabled_in_edit_mode', (bool) $this->post('enabledInEditMode'));
        $config->save('speed_analyzer.reports.enabled_in_dashboard', (bool) $this->post('enabledInDashboard'));
        $config->save('speed_analyzer.reports.override_event_dispatcher', (bool) $this->post('overrideEventDispatcher'));
        $config->save('speed_analyzer.reports.overwrite_reports', (bool) $this->post('overwriteReports'));
        $config->save('speed_analyzer.reports.log_sql_queries', (bool) $this->post('logSqlQueries'));
        $config->save('speed_analyzer.reports.track_ajax_requests', (bool) $this->post('trackAjaxRequests'));
        $config->save('speed_analyzer.reports.custom_events', $this->getCustomEvents($this->post('customEvents', '')));
        $config->save('speed_analyzer.reports.write_if_exec_time_longer_than', $this->post('writeIfExecTimeLongerThan'));

        $this->flash('success', t('Your settings have been saved.'));

        return Redirect::to('/dashboard/speed_analyzer/settings');
    }

    /**
     * @param $postData
     *
     * @return array
     */
    private function getCustomEvents($postData)
    {
        $events = explode("\n", str_replace("\r", '', $postData));

        return array_map('trim', $events);
    }
}
