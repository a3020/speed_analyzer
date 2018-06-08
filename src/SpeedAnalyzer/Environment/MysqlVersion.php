<?php

namespace A3020\SpeedAnalyzer\Environment;

use Concrete\Core\Database\Connection\Connection;

class MysqlVersion
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get()
    {
        return $this->connection->fetchColumn('select version()');
    }
}
