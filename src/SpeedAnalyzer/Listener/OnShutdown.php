<?php

namespace A3020\SpeedAnalyzer\Listener;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use A3020\SpeedAnalyzer\Report\WriteReport;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Support\Facade\Log;
use Exception;

class OnShutdown extends BaseTrack implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function handle($event)
    {
        $reportEvent = new ReportEvent();
        $reportEvent->setEvent('on_shutdown');

        $this->track($reportEvent);

        try {
            $this->app->make(WriteReport::class)->toDatabase();
        } catch (Exception $e) {
            Log::addDebug($e->getMessage());
        }
    }
}
