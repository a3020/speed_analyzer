<?php

namespace A3020\SpeedAnalyzer\Event;

class EventCategory
{
    const APPLICATION = 1;
    const USER = 2;
    const PAGE = 3;
    const FILE = 4;
    const BLOCK = 5;

    // Other concrete5 events that do not fit in categories above
    const OTHER = 6;

    // Custom events from other packages or code
    const CUSTOM = 99;

    private $event;
    private $category;

    public function __construct($event, $category)
    {
        $this->event = $event;
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        switch ($this->category) {
            case self::APPLICATION:
                return t('Application');
            case self::USER:
                return t('User');
            case self::PAGE:
                return t('Page');
            case self::FILE:
                return t('File');
            case self::BLOCK:
                return t('Block');
            case self::OTHER:
                return t('Other');
        }

        return t('Custom');
    }

    /**
     * @return string
     */
    public function getCategoryColor()
    {
        switch ($this->category) {
            case self::APPLICATION:
                return '#0fa919';
            case self::USER:
                return '#a9780f';
            case self::PAGE:
                return '#0fa4a9';
            case self::FILE:
                return '#a90f91';
            case self::BLOCK:
                return '#0b6bdc';
            case self::OTHER:
                return '#0f87a9';
        }

        return '#880fa9';
    }
}
