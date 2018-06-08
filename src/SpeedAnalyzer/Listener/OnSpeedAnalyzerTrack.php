<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use A3020\SpeedAnalyzer\Event\TrackEvent;

class OnSpeedAnalyzerTrack extends BaseTrack
{
    /**
     * @param TrackEvent|null $event
     */
    public function handle($event = null)
    {
        $reportEvent = new ReportEvent();
        $reportEvent->setEvent('on_speed_analyzer_track');

        if ($event instanceof TrackEvent) {
            $reportEvent->setData($event->getData());
        }

        $this->track($reportEvent);
    }
}
