<?php

namespace Level3\Resource\Format\Writer;

use Level3\Resource\Format\Writer;
use Level3\Resource\Resource;
use Hampel\Json\Json;

abstract class JsonWriter extends Writer
{
    public function execute(Resource $resource)
    {
        $options = [
            'unescaped_slashes' => true
        ];

        if ($this->pretty) {
            $options['pretty_print'] = true;
        }

        return Json::encode($this->resourceToArray($resource), $options);
    }

    abstract protected function resourceToArray(Resource $resource);
}
