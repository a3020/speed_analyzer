<?php

namespace A3020\SpeedAnalyzer\Log;

use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Logging\DebugStack;

class QueryLogger
{
    public function __construct(Connection $connection, DebugStack $debugStack)
    {
        $connection
            ->getConfiguration()
            ->setSQLLogger($debugStack);
    }
}
