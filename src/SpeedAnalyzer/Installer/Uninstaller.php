<?php

namespace A3020\SpeedAnalyzer\Installer;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Log;
use Exception;

class Uninstaller
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function uninstall($pkg)
    {
        try {
            $this->connection->executeQuery("DROP TABLE IF EXISTS SpeedAnalyzerReportEventQueries");
            $this->connection->executeQuery("DROP TABLE IF EXISTS SpeedAnalyzerReportEvents");
            $this->connection->executeQuery("DROP TABLE IF EXISTS SpeedAnalyzerReports");
        } catch (Exception $e) {
            Log::addDebug($e->getMessage());
        }
    }
}
