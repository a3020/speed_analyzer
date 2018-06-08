<?php

namespace A3020\SpeedAnalyzer\Report;

use A3020\SpeedAnalyzer\Entity\Report;
use Doctrine\ORM\EntityManager;

class ReportRepository
{
    /** @var EntityManager */
    private $entityManager;

    /** @var \Doctrine\ORM\EntityRepository */
    protected $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Report::class);
    }

    /**
     * Find a Report by guid
     *
     * @param string $id
     *
     * @return Report|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Find a Report by page id
     *
     * @param int $pageId
     *
     * @return Report|null
     */
    public function findByPageId($pageId)
    {
        return $this->repository->findOneBy([
            'pageId' => $pageId
        ]);
    }

    /**
     * Delete one Report
     *
     * @param string $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return bool
     */
    public function delete($id)
    {
        $report = $this->find($id);
        if (!$report) {
            return false;
        }

        $this->entityManager->remove($report);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $userId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return bool
     */
    public function deleteByUserId($userId)
    {
        $reports = $this->repository->findBy([
            'userId' => $userId
        ]);

        if (count($reports) === 0) {
            return false;
        }

        foreach ($reports as $report) {
            $this->entityManager->remove($report);
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * Delete all Reports and associated data
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAll()
    {
        $reports = $this->repository->findAll();

        foreach ($reports as $report) {
            $this->entityManager->remove($report);
        }

        $this->entityManager->flush();

        // Just to make sure everything is really deleted. (also for development reasons)
        $this->entityManager->getConnection()->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->entityManager->getConnection()->exec("TRUNCATE TABLE SpeedAnalyzerReportEventQueries");
        $this->entityManager->getConnection()->exec("TRUNCATE TABLE SpeedAnalyzerReportEvents");
        $this->entityManager->getConnection()->exec("TRUNCATE TABLE SpeedAnalyzerReports");
        $this->entityManager->getConnection()->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
}
