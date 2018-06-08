<?php

namespace A3020\SpeedAnalyzer\Request;

use JsonSerializable;

class RequestData implements JsonSerializable
{
    protected $cacheSettings = [];

    /**
     * Used to load an array representation from the database into an object
     *
     * @param array $data
     *
     * @return static
     */
    public static function fromData($data)
    {
        $obj = new static();

        foreach ($data['cache'] as $name => $value) {
            $obj->addCacheSetting($name, $value);
        }

        return $obj;
    }

    /**
     * @param string $name
     * @param bool $value
     */
    public function addCacheSetting($name, $value)
    {
        $this->cacheSettings[$name] = (bool) $value;
    }

    /**
     * @param string $name
     *
     * @return bool|null
     */
    public function getCacheSetting($name)
    {
        if (!isset($this->cacheSettings[$name])) {
            return;
        }

        return (bool) $this->cacheSettings[$name];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'cache' => $this->cacheSettings,
        ];
    }
}
