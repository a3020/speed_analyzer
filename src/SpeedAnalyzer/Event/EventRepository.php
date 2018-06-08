<?php

namespace A3020\SpeedAnalyzer\Event;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use Doctrine\ORM\EntityManager;

class EventRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(ReportEvent::class);
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
