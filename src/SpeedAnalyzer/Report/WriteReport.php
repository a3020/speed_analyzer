<?php

namespace A3020\SpeedAnalyzer\Report;

use A3020\SpeedAnalyzer\Entity\Report;
use A3020\SpeedAnalyzer\Request\RequestData;
use A3020\SpeedAnalyzer\Request\Tracker;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class WriteReport implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var EntityManager */
    private $entityManager;

    /** @var Repository */
    private $config;

    /** @var Request */
    private $request;

    /** @var Tracker */
    private $tracker;

    public function __construct(EntityManager $entityManager, Repository $config, Request $request, Tracker $tracker)
    {
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->request = $request;
        $this->tracker = $tracker;
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function toDatabase()
    {
        /** @var Page $page */
        $page = Page::getCurrentPage();
        if (!$this->shouldMakeReport($page)) {
            return;
        }

        $report = $this->makeReport($page);

        // The times are in microseconds
        $startTime = $endTime = 0;

        foreach ($this->tracker->get() as $reportEvent) {
            if ($startTime === 0) {
                $startTime = $reportEvent->getRecordedTime();
            }

            $endTime = $recordedTime = $reportEvent->getRecordedTime() - $startTime;
            $reportEvent->setRecordedTime($recordedTime);

            $report->addEvent($reportEvent);
        }

        if (!$this->wasExecutionTimeLongEnough($endTime)) {
            return;
        }

        $report->setTotalExecutionTime($endTime);
        $this->entityManager->persist($report);
        $this->entityManager->flush();
    }

    /**
     * @param Page $page
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function deleteOldRecords(Page $page)
    {
        foreach ($this->entityManager->getRepository(Report::class)->findBy([
            'pageId' => $page->getCollectionID(),
        ]) as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @return \A3020\SpeedAnalyzer\Request\RequestData
     */
    private function createRequestData()
    {
        /** @var RequestData $requestData */
        $requestData = $this->app->make(RequestData::class);

        $requestData->addCacheSetting('blocks', $this->config->get('concrete.cache.blocks'));
        $requestData->addCacheSetting('theme_css', $this->config->get('concrete.cache.theme_css'));
        $requestData->addCacheSetting('compress_preprocessor_output', $this->config->get('concrete.theme.compress_preprocessor_output'));
        $requestData->addCacheSetting('assets', $this->config->get('concrete.cache.assets'));
        $requestData->addCacheSetting('overrides', $this->config->get('concrete.cache.overrides'));

        return $requestData;
    }

    /**
     * @param Page $page
     *
     * @return Report
     */
    private function makeReport(Page $page)
    {
        $report = new Report();
        $report->setPageId($page->getCollectionID());
        $report->setPageName($page->getCollectionName());

        $user = new User();
        if ($user) {
            $report->setUserId($user->getUserID());
        }

        $report->setRequestUri($this->request->getUri());
        $report->setRequestMethod($this->request->getMethod());
        $report->setRequestIsAjax($this->request->isXmlHttpRequest());
        $report->setRequestData($this->createRequestData());

        $this->entityManager->persist($report);

        return $report;
    }

    /**
     * Return true if we've reached x-number of Reports
     *
     * The table can simply overload if the user has forgotten
     * to disable the analyzer or to enable 'overwrite reports'.
     *
     * @param int $max
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return bool
     */
    private function hasTooManyReports($max = 1000)
    {
        $numberOfRecords = $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(Report::class, 'r')
            ->getQuery()
            ->getSingleScalarResult();

        return $numberOfRecords >= $max;
    }

    /**
     * Should a make a report or ignore the gathered data?
     *
     * @param Page $page
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return bool
     */
    private function shouldMakeReport($page)
    {
        // Extra checks now we have access to the Page object
        if (!$page || $page->isError()) {
            return false;
        }

        // Do not track when the page is in edit mode (configurable)
        if ($this->config->get('speed_analyzer.reports.enabled_in_edit_mode', false) === false && $page->isEditMode()) {
            return false;
        }

        // Do not track the dashboard area (configurable)
        if ($this->config->get('speed_analyzer.reports.enabled_in_dashboard', false) === false && $page->isAdminArea()) {
            return false;
        }

        $overwriteReports = $this->config->get('speed_analyzer.reports.overwrite_reports', false);
        if ($overwriteReports) {
            $this->deleteOldRecords($page);
        }

        if ($overwriteReports === false
            && $this->hasTooManyReports((int) $this->config->get('speed_analyzer.reports.hard_limit', 75))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return true if execution time was long enough to make a report
     *
     * The minimum request time can be configured. If it is configured
     * but smaller than the current request time, we don't want to make a report.
     *
     * (we haven't flushed at this point)
     *
     * @param int $endTime
     *
     * @return bool
     */
    private function wasExecutionTimeLongEnough($endTime)
    {
        // Only write report if the request took longer than ... milliseconds
        $minRequestTime = $this->config->get('speed_analyzer.reports.write_if_exec_time_longer_than', 0);
        if ($minRequestTime && $minRequestTime > ($endTime * 1000)) {
            return false;
        }

        return true;
    }
}
