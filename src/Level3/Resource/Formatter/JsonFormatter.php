<?php

namespace Level3\Resource\Formatter;

use Level3\Resource\Formatter;
use Level3\Resource\Resource;

abstract class JsonFormatter extends Formatter
{
    public function fromRequest($string)
    {
        if (strlen($string) == 0) {
            return [];
        }

        $array = json_decode($string, true);

        if (!is_array($array)) {
            return null;
        }

        return $array;
    }

    public function toResponse(Resource $resource, $pretty = false)
    {
        $options = JSON_UNESCAPED_SLASHES;

        if ($pretty) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        return json_encode($this->resourceToArray($resource), $options);
    }

    abstract protected function resourceToArray(Resource $resource);
}
