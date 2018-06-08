<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use Concrete\Core\Support\Facade\Facade;

class Track extends BaseTrack
{
    /**
     * @param string $eventName
     * @param mixed $event
     */
    public function handle($eventName, $event)
    {
        if ($this->loadCustomListener($eventName, $event)) {
            return;
        }

        $reportEvent = new ReportEvent();
        $reportEvent->setEvent($eventName);

        $this->track($reportEvent);
    }

    private function loadCustomListener($eventName, $event)
    {
        $listeners = [
            'on_block_before_render' => \A3020\SpeedAnalyzer\Listener\OnBlockBeforeRender::class,
            'on_block_load' => \A3020\SpeedAnalyzer\Listener\OnBlockLoad::class,
            'on_shutdown' => \A3020\SpeedAnalyzer\Listener\OnShutdown::class,
            'on_speed_analyzer_track' => \A3020\SpeedAnalyzer\Listener\OnSpeedAnalyzerTrack::class,
        ];

        if (array_key_exists($eventName, $listeners)) {
            $app = Facade::getFacadeApplication();
            $listener = $app->make($listeners[$eventName]);
            $listener->handle($event);

            return true;
        }
    }
}
