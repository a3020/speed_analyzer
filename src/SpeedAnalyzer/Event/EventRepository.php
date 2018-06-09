<?php

namespace A3020\SpeedAnalyzer\Event;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use Doctrine\ORM\EntityRepository;

class EventRepository
{
    /** @var EntityRepository */
    protected $repository;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find a ReportEvent by guid
     *
     * @param string $id
     *
     * @return ReportEvent|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }
}
