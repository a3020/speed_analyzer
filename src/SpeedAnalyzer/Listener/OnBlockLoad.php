<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

class OnBlockLoad extends BaseTrack
{
    /**
     * @param GenericEvent $event
     */
    public function handle($event)
    {
        $reportEvent = new ReportEvent();
        $reportEvent->setEvent('on_block_load');
        $reportEvent->setData([
            'block_id' => $event->getArgument('bID'),
            'block_handle' => $event->getArgument('btHandle'),
        ]);

        $this->track($reportEvent);
    }
}
