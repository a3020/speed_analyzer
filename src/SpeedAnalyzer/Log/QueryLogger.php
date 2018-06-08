<?php

namespace A3020\SpeedAnalyzer\Log;

use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Logging\DebugStack;

class QueryLogger
{
    public function __construct(DebugStack $debugStack, Connection $connection)
    {
        $connection
            ->getConfiguration()
            ->setSQLLogger($debugStack);
    }
}
