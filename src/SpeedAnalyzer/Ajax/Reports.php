<?php

namespace A3020\SpeedAnalyzer\Ajax;

use A3020\SpeedAnalyzer\Entity\Report;
use A3020\SpeedAnalyzer\Report\ReportList;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;

class Reports extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function getPage()
    {
        $this->checkPermissions();

        /** @var ReportList $list */
        $list = $this->app->make(ReportList::class);
        $list->sortByTotalExecutionTimeDesc();
        $list->sortBy($this->getSortColumn(), $this->getSortDirection());

        $itemsPerPage = (int) $this->request->get('length', 10);

        $pagination = $list->getPagination();
        $pagination->setMaxPerPage($itemsPerPage);

        $start = (int) $this->request->get('start', 0);
        $pagination->setCurrentPage(($start / $itemsPerPage) + 1);

        $json = [
            'draw' => (int) $this->request->get('draw', 1),
            'recordsTotal' => $list->getTotalResults(),
            'recordsFiltered' => $list->getTotalResults(),
            'data' => [],
        ];

        /** @var \Concrete\Core\Localization\Service\Date $dh */
        $dh = $this->app->make('helper/date');

        /** @var Report $report */
        foreach ($pagination->getCurrentPageResults() as $report) {
            $user = $report->getUser();
            $userName = $user ? $user->getUserName() : t('None');

            $json['data'][] = [
                'id' => $report->getId(),
                'page_id' => $report->getPageId(),
                'page_name' => $report->getPageName(),
                'request_uri' => $report->getRequestUri(),
                'request_method' => $report->getRequestMethod(),
                'is_ajax' => $report->isAjaxRequest() ? t('Yes') : t('No'),
                'user' => $userName,
                'created_at' => $dh->formatDateTime($report->getCreatedAt()),
                'execution_time' => number_format($report->getTotalExecutionTime() * 1000) .' '.t('ms'),
            ];
        }

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/speed_analyzer');
        $cp = new \Permissions($page);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            throw new UserMessageException(t('Access Denied'));
        }
    }

    private function getSortColumn()
    {
        $order = $this->request->get('order');
        if (!isset($order[0]['column'])) {
            return 'createdAt';
        }

        switch ($order[0]['column']) {
            case 0:
                return 'pageName';
            break;
            case 1:
                return 'userId';
            break;
            case 2:
                return 'totalExecutionTime';
            case 3:
                return 'requestMethod';
            case 4:
                return 'requestIsAjax';
            default:
                return 'createdAt';
        }
    }

    /**
     * Return 'asc' or 'desc'
     *
     * @return string
     */
    private function getSortDirection()
    {
        $direction = $this->request->get('order');
        if (!isset($direction[0]['dir']) || !in_array($direction[0]['dir'], ['asc', 'desc'])) {
            return 'desc';
        }

        return $direction[0]['dir'];
    }
}
