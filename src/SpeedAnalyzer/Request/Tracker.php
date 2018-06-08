<?php

namespace A3020\SpeedAnalyzer\Request;

use A3020\SpeedAnalyzer\Entity\ReportEvent;

class Tracker
{
    protected $events = [];

    /**
     * @param ReportEvent $reportEvent
     */
    public function add(ReportEvent $reportEvent)
    {
        $this->events[] = $reportEvent;
    }

    /**
     * @return ReportEvent[]
     */
    public function get()
    {
        return $this->events;
    }
}
