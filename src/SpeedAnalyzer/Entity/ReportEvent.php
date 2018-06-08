<?php

namespace A3020\SpeedAnalyzer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="SpeedAnalyzerReportEvents",
 * )
 */
class ReportEvent
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Report")
     * @ORM\JoinColumn(name="reportId", referencedColumnName="id")
     **/
    protected $report;

    /**
     * @ORM\Column(type="string")
     */
    protected $event;

    /**
     * @ORM\Column(type="float")
     */
    protected $recordedTime;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $data = [];

    /**
     * @ORM\OneToMany(targetEntity="ReportEventQuery", mappedBy="event", cascade={"persist", "remove"})
     */
    protected $queries = [];

    public function __construct()
    {
        $this->recordedTime = microtime(true);
        $this->queries = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * In seconds
     *
     * @return float
     */
    public function getRecordedTime()
    {
        return $this->recordedTime;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return ReportEventQuery[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param string $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * In seconds
     *
     * @param float $recordedTime
     */
    public function setRecordedTime($recordedTime)
    {
        $this->recordedTime = $recordedTime;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function setReport(Report $report)
    {
        $this->report = $report;
    }

    public function addQuery(ReportEventQuery $reportEventQuery)
    {
        $reportEventQuery->setEvent($this);

        $this->queries->add($reportEventQuery);
    }

    public function getNumberOfQueries()
    {
        return count($this->getQueries());
    }

    /**
     * Query time in milliseconds
     *
     * @return float
     */
    public function getTotalQueryTime()
    {
        $total = 0;
        foreach ($this->getQueries() as $query) {
            $total += $query->getExecutionTime();
        }

        return $total * 1000;
    }
}
