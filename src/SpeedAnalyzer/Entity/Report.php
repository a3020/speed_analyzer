<?php

namespace A3020\SpeedAnalyzer\Entity;

use A3020\SpeedAnalyzer\Request\RequestData;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="SpeedAnalyzerReports",
 * )
 */
class Report
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=false)
     */
    protected $pageId;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $userId;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $pageName;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * In microseconds
     *
     * @ORM\Column(type="float")
     */
    protected $totalExecutionTime = 0;

    /**
     * This is the full URL of the requested resource
     *
     * @ORM\Column(type="string", length=512)
     */
    protected $requestUri;

    /**
     * E.g. GET, POST, PUT, etc.
     *
     * @ORM\Column(type="string", length=10)
     */
    protected $requestMethod;

    /**
     * Whether the request was sent with AJAX
     *
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $requestIsAjax = false;

    /**
     * Request related data that is not necessary to filter or sort on
     *
     * @ORM\Column(type="json_array")
     */
    protected $requestData;

    /**
     * The associated event objects
     *
     * @ORM\OneToMany(targetEntity="ReportEvent", mappedBy="report", cascade={"persist", "remove"})
     */
    protected $events;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->events = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return Page::getByID($this->getPageId());
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return (string) $this->pageName;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return ReportEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param int $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = (int) $pageId;
    }

    /**
     * @return float
     */
    public function getTotalExecutionTime()
    {
        return (float) $this->totalExecutionTime;
    }

    /**
     * @return mixed
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @return bool
     */
    public function isAjaxRequest()
    {
        return $this->requestIsAjax;
    }

    /**
     * @return RequestData
     */
    public function getRequestData()
    {
        return RequestData::fromData($this->requestData);
    }

    /**
     * In milliseconds
     *
     * @return float|int
     */
    public function getTotalQueryTime()
    {
        $total = 0;
        foreach ($this->getEvents() as $event) {
            $total += $event->getTotalQueryTime();
        }

        return $total;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return User::getByUserID($this->userId);
    }

    public function addEvent(ReportEvent $reportEvent)
    {
        $reportEvent->setReport($this);

        $this->events->add($reportEvent);
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = (int) $userId;
    }

    /**
     * @param string $pageName
     */
    public function setPageName($pageName)
    {
        $this->pageName = (string) $pageName;
    }

    /**
     * @param float $totalExecutionTime
     */
    public function setTotalExecutionTime($totalExecutionTime)
    {
        $this->totalExecutionTime = (float) $totalExecutionTime;
    }

    /**
     * @param mixed $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * @param string $requestMethod
     */
    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * @param bool $requestIsAjax
     */
    public function setRequestIsAjax($requestIsAjax)
    {
        $this->requestIsAjax = $requestIsAjax;
    }

    /**
     * @param \A3020\SpeedAnalyzer\Request\RequestData $requestData
     */
    public function setRequestData($requestData)
    {
        $this->requestData = $requestData;
    }
}
