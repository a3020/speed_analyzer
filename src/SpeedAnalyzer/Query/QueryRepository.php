<?php

namespace A3020\SpeedAnalyzer\Query;

use A3020\SpeedAnalyzer\Entity\ReportEventQuery;
use Doctrine\ORM\EntityManager;

class QueryRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(ReportEventQuery::class);
    }

    /**
     * Find a Query by guid
     *
     * @param string $id
     *
     * @return ReportEventQuery|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findBy($data)
    {
        return $this->repository->findBy($data);
    }
}
