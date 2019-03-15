<?php

namespace A3020\SpeedAnalyzer\Installer;

use Concrete\Core\Database\Connection\Connection;
use Psr\Log\LoggerInterface;
use Exception;

class Uninstaller
{
    /** @var \Concrete\Core\Database\Connection\Connection */
    private $connection;

    /** @var Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function uninstall()
    {
        foreach ([
            'SpeedAnalyzerReportEventQueries',
            'SpeedAnalyzerReportEvents',
            'SpeedAnalyzerReports',
        ] as $tableName) {
            $this->dropTable($tableName);
        }
    }

    private function dropTable($tableName)
    {
        try {
            $this->connection->executeQuery("DROP TABLE IF EXISTS ".$tableName);
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
