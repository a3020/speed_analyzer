<?php

namespace A3020\SpeedAnalyzer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="SpeedAnalyzerReportEventQueries",
 * )
 */
class ReportEventQuery
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ReportEvent")
     * @ORM\JoinColumn(name="eventId", referencedColumnName="id")
     **/
    protected $event;

    /**
     * @ORM\Column(type="text")
     */
    protected $query;

    /**
     * @ORM\Column(type="float")
     */
    protected $executionTime;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ReportEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * In microseconds!
     *
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * @param ReportEvent $event
     */
    public function setEvent(ReportEvent $event)
    {
        $this->event = $event;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * In microseconds!
     *
     * @param float $executionTime
     */
    public function setExecutionTime($executionTime)
    {
        $this->executionTime = $executionTime;
    }
}
