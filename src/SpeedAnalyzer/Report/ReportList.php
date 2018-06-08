<?php

namespace A3020\SpeedAnalyzer\Report;

use A3020\SpeedAnalyzer\Entity\Report;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class ReportList extends ItemList implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function createQuery()
    {
        $this->query->select('r.id')
            ->from('SpeedAnalyzerReports', 'r');
    }

    /**
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject()->resetQueryParts();

        return (int) $query->select('COUNT(1)')
            ->from('SpeedAnalyzerReports')
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();
    }

    /**
     * @param $queryRow
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return Report
     */
    public function getResult($queryRow)
    {
        $repository = $this->app->make(EntityManager::class)->getRepository(Report::class);

        return $repository->findOneById($queryRow['id']);
    }

    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    public function sortByTotalExecutionTimeDesc()
    {
        $this->sortBy('totalExecutionTime', 'desc');
    }
}
