<?php

namespace A3020\SpeedAnalyzer\Report;

use A3020\SpeedAnalyzer\Entity\ReportEvent;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Support\Facade\Url;
use JsonSerializable;

class ReportEventInformation implements JsonSerializable
{
    protected $entries = [];

    public function __construct(ReportEvent $reportEvent)
    {
        $data = $reportEvent->getData();
        if (isset($data['block_handle'])) {
            /** @var \Concrete\Core\Entity\Block\BlockType\BlockType $bt */
            $bt = BlockType::getByHandle($data['block_handle']);
            if ($bt) {
                $link = Url::to('/dashboard/blocks/types/inspect/'.$bt->getBlockTypeID());
                $value = $bt->getBlockTypeName();
                $html = '<a target="_blank" href="'.$link.'">'.$bt->getBlockTypeName().'</a>';
            } else {
                $value = $data['block_handle'];
                $html = $data['block_handle'];
            }

            $this->add(t('Block Type'), $value, $html);
        }

        if (isset($data['block_id'])) {
            $this->add(t('Block ID'), $data['block_id'], $data['block_id']);
        }

        if ($reportEvent->getEvent() === 'on_speed_analyzer_track') {
            $data = $reportEvent->getData();
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $this->add($key, json_encode($value), json_encode($value));
                } else {
                    $this->add($key, $value, $value);
                }
            }
        }
    }

    /**
     * The table under the graph can display HTML
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '';
        foreach ($this->entries as $entry) {
            if (is_numeric($entry['type'])) {
                $html .= $entry['valueHtml'].'<br>';
                continue;
            }

            $html .= t($entry['type']) .': '.$entry['valueHtml'].'<br>';
        }

        return $html;
    }


    /**
     * @param string $type
     * @param string $value
     * @param string $valueHtml
     */
    private function add($type, $value, $valueHtml = '')
    {
        $this->entries[] = [
            'type' => $type,
            'value' => $value,
            'valueHtml' => $valueHtml,
        ];
    }

    /**
     * The graph will use the json representation
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->entries;
    }
}
