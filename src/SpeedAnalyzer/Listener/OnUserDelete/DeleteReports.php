<?php

namespace A3020\SpeedAnalyzer\Listener\OnUserDelete;

use A3020\SpeedAnalyzer\Report\ReportRepository;
use Exception;

class DeleteReports
{
    /** @var \A3020\SpeedAnalyzer\Report\ReportRepository */
    private $repository;

    public function __construct(ReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Removes all reports if a user is deleted
     *
     * @param \Concrete\Core\User\Event\DeleteUser $event
     */
    public function handle($event)
    {
        try {
            $this->repository->deleteByUserId($event->getUserInfoObject()->getUserID());
        } catch (Exception $e) {
            \Log::addDebug($e->getMessage());
        }
    }
}
