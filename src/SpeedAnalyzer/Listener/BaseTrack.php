<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use A3020\SpeedAnalyzer\Entity\ReportEventQuery;
use A3020\SpeedAnalyzer\Query\RealQuery;
use A3020\SpeedAnalyzer\Request\Tracker;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;

class BaseTrack implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Tracker */
    private $tracker;

    /** @var Repository */
    private $config;

    /** @var RealQuery */
    private $realQuery;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Tracker $tracker, Connection $connection, Repository $repository, RealQuery $realQuery)
    {
        $this->tracker = $tracker;
        $this->connection = $connection;
        $this->config = $repository;
        $this->realQuery = $realQuery;
    }

    public function track(ReportEvent $reportEvent)
    {
        $this->trackQueries($reportEvent);
        $this->tracker->add($reportEvent);
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
        $logger = $this->connection->getConfiguration()->getSQLLogger();

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
