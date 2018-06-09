<?php

namespace A3020\SpeedAnalyzer\Event;

interface TrackEventInterface
{
    /**
     * Get analysis / debug data attached to this event
     *
     * @return array
     */
    public function getData();

    /**
     * Set analysis / debug data to this event.
     *
     * We eventually want to display the data, so we
     * prefer arrays over objects.
     *
     * @param array $data
     */
    public function setData(array $data);
}
