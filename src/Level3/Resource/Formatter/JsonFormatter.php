<?php

namespace Level3\Resource\Formatter;

use Level3\Resource\Formatter;
use Level3\Resource\Resource;
use Level3\Exceptions\BadRequest;

abstract class JsonFormatter extends Formatter
{
    public function fromRequest($string)
    {
        if (strlen($string) == 0) {
            return [];
        }

        $array = json_decode($string, true);

        if (!is_array($array)) {
            throw new BadRequest();
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
