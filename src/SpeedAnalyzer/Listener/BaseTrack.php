<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use A3020\SpeedAnalyzer\Entity\ReportEventQuery;
use A3020\SpeedAnalyzer\Query\RealQuery;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;

class BaseTrack implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var EntityManager */
    private $entityManager;

    /** @var Repository */
    private $config;
    /**
     * @var RealQuery
     */
    private $realQuery;

    public function __construct(EntityManager $entityManager, Repository $repository, RealQuery $realQuery)
    {
        $this->entityManager = $entityManager;
        $this->config = $repository;
        $this->realQuery = $realQuery;
    }

    public function track(ReportEvent $reportEvent)
    {
        $this->trackQueries($reportEvent);

        /** @var \A3020\SpeedAnalyzer\Request\Tracker $tracker */
        $tracker = $this->app->make('speed_analyzer_tracker');
        $tracker->add($reportEvent);
    }

    /**
     * @param ReportEvent $reportEvent
     */
    private function trackQueries(ReportEvent $reportEvent)
    {
        if (! (bool) $this->config->get('speed_analyzer.reports.log_sql_queries', true)) {
            return;
        }

        /** @var DebugStack $logger */
        $logger = $this->entityManager->getConnection()->getConfiguration()->getSQLLogger();

        foreach ($logger->queries as $debugResult) {
            $reportEventQuery = new ReportEventQuery();

            $reportEventQuery->setQuery($this->realQuery->getSqlQuery($debugResult['sql'], $debugResult['params']));
            $reportEventQuery->setExecutionTime($debugResult['executionMS']);

            $reportEvent->addQuery($reportEventQuery);
        }

        // Reset the logger
        $logger->queries = [];
        $logger->currentQuery = 0;
    }
}
