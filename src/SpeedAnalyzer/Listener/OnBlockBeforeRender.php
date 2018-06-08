<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;

class OnBlockBeforeRender extends BaseTrack
{
    /**
     * Note: This event is only supported in concrete5 8.4.0 and up.
     *
     * @param \Concrete\Core\Block\Events\BlockBeforeRender $event
     */
    public function handle($event)
    {
        if (!$event->getBlock()) {
            return;
        }

        $reportEvent = new ReportEvent();
        $reportEvent->setEvent('on_block_before_render');
        $reportEvent->setData([
            'block_id' => $event->getBlock()->getBlockID(),
            'block_handle' => $event->getBlock()->getBlockTypeHandle(),
        ]);

        $this->track($reportEvent);
    }
}
