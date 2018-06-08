<?php

namespace Concrete\Package\SpeedAnalyzer\Controller\SinglePage\Dashboard\SpeedAnalyzer;

use A3020\SpeedAnalyzer\Entity\Report;
use A3020\SpeedAnalyzer\Entity\ReportEvent;
use A3020\SpeedAnalyzer\Event\EventCategoryService;
use A3020\SpeedAnalyzer\Event\EventInfo;
use A3020\SpeedAnalyzer\Report\ReportList;
use A3020\SpeedAnalyzer\Report\ReportRepository;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;

final class Reports extends DashboardPageController
{
    public function on_before_render()
    {
        parent::on_before_render();

        $al = AssetList::getInstance();

        $al->register('javascript', 'speed_analyzer/datatables', 'js/datatables.min.js', [], 'speed_analyzer');
        $al->register('javascript', 'speed_analyzer/chartjs', 'js/Chart.bundle.min.js', [], 'speed_analyzer');
        $al->register('javascript', 'speed_analyzer/chartjs-plugin-annotation', 'js/chartjs-plugin-annotation.js', [], 'speed_analyzer');
        $this->requireAsset('javascript', 'speed_analyzer/datatables');
        $this->requireAsset('javascript', 'speed_analyzer/chartjs');
        $this->requireAsset('javascript', 'speed_analyzer/chartjs-plugin-annotation');

        $al->register('css', 'speed_analyzer/style', 'css/style.css', [], 'speed_analyzer');
        $al->register('css', 'speed_analyzer/datatables', 'css/datatables.css', [], 'speed_analyzer');
        $this->requireAsset('css', 'speed_analyzer/style');
        $this->requireAsset('css', 'speed_analyzer/datatables');
    }

    /**
     * DataTable with pages for which reports have been generated
     */
    public function view()
    {
        $config = $this->app->make(Repository::class);

        /** @var \A3020\SpeedAnalyzer\Report\ReportList $list */
        $list = $this->app->make(ReportList::class);

        $this->set('isEnabled', (bool) $config->get('speed_analyzer.enabled', false));
        $this->set('isFullPageCachingEnabled', $config->get('concrete.cache.pages') === 'all');
        $this->set('hasData', (bool) $list->getTotalResults());
    }

    /**
     * Show a report
     *
     * @param string $id Report id
     * @param int $pageId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function details($id, $pageId)
    {
        /** @var Report $report */
        $report = $this->app->make(ReportRepository::class)->find($id);
        if (!$report) {
            $page = Page::getByID($pageId);
            $report = $this->app->make(ReportRepository::class)->findByPageId($page->getCollectionID());

            if (!$report) {
                $this->flash('error', t('Report not found.'));

                return Redirect::to('/dashboard/speed_analyzer/reports');
            }
        }

        $this->set('pageTitle', t("Report for '%s'", $report->getPageName()));
        $reportPage = $report->getPage();
        if ($reportPage && !$reportPage->isError()) {
            $this->set('reportPage', $reportPage);
        }

        $items = $this->getItems($report->getEvents());

        if (count($items) <= 1) {
            $this->flash('success', t('This report does not contain enough data to be displayed.'));

            return Redirect::to('/dashboard/speed_analyzer/reports');
        }

        $this->set('dh', $this->app->make('helper/date'));
        $this->set('eventHelper', $this->app->make(EventInfo::class));
        $this->set('report', $report);
        $this->set('items', $items);
        $this->set('lineAnnotations', $this->getLineAnnotations($items));
        $this->set('logSqlQueries', (bool) $this->app->make(Repository::class)->get('speed_analyzer.reports.log_sql_queries', true));

        return $this->render('/dashboard/speed_analyzer/details');
    }

    public function delete($id)
    {
        $result = $this->app->make(ReportRepository::class)->delete($id);

        if ($result) {
            $this->flash('success', t('Reports has been deleted.'));
        }

        return Redirect::to('/dashboard/speed_analyzer/reports');
    }

    public function deleteAll()
    {
        $this->app->make(ReportRepository::class)->deleteAll();

        $this->flash('success', t('All reports have been deleted.'));

        return Redirect::to('/dashboard/speed_analyzer/reports');
    }

    /**
     * Create an array with information for the graph / table
     *
     * We enrich the existing data with e.g. differences in ms
     * between the time records.
     *
     * @param ReportEvent[] $reportEvents
     *
     * @return array
     */
    private function getItems($reportEvents)
    {
        /** @var EventCategoryService $eventCategoryService */
        $eventCategoryService = $this->app->make(EventCategoryService::class);

        $data = [];
        $i = 0;
        foreach ($reportEvents as $reportEvent) {
            $eventCategory = $eventCategoryService->find($reportEvent->getEvent());
            $item = [
                'id' => $i,
                'event_id' => $reportEvent->getId(),
                'time' => $reportEvent->getRecordedTime() * 1000,
                'event' => $reportEvent->getEvent(),
                'event_category' => $eventCategory->getCategory(),
                'event_category_color' => $eventCategory->getCategoryColor(),
                'information' => new \A3020\SpeedAnalyzer\Report\ReportEventInformation($reportEvent),
                'difference' => 0,
                'number_of_queries' => $reportEvent->getNumberOfQueries(),
                'total_query_time_exact' => $reportEvent->getTotalQueryTime(),
                'total_query_time_rounded' => number_format($reportEvent->getTotalQueryTime(), 2),
            ];

            if (isset($data[$i - 1])) {
                $item['difference'] = $item['time'] - $data[$i - 1]['time'];
            }

            $data[] = $item;

            $i++;
        }

        return $data;
    }

    private function getLineAnnotations($items)
    {
        $annotations = [];
        foreach ($items as $item) {
            if ($item['event'] === 'on_before_render') {
                $annotations[] = [
                    'label' => 'on_before_render',
                    'value' => $item['time'],
                    'position' => 'top',
                    'xAdjust' => 0,
                ];
            }

            if ($item['event'] === 'on_render_complete') {
                $annotations[] = [
                    'label' => 'on_render_complete',
                    'value' => $item['time'],
                    'position' => 'bottom',
                    'xAdjust' => 40,
                ];
            }
        }

        return $annotations;
    }
}
