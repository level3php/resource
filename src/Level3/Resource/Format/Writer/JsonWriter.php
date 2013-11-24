<?php

namespace Level3\Resource\Format\Writer;

use Level3\Resource\Format\Writer;
use Level3\Resource\Resource;

abstract class JsonWriter extends Writer
{
    public function execute(Resource $resource)
    {
        $options = JSON_UNESCAPED_SLASHES;

        if ($this->pretty) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        return json_encode($this->resourceToArray($resource), $options);
    }

    abstract protected function resourceToArray(Resource $resource);
}
