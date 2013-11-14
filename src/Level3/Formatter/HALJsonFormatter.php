<?php

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Exceptions\BadRequest;

class HALJsonFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/hal+json';

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

        return json_encode($resource->toArray(), $options);
    }
}
