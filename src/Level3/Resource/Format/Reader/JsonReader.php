<?php

namespace Level3\Resource\Format\Reader;

use Level3\Resource\Format\Reader;
use Hampel\Json\Json;
use Hampel\Json\JsonException;

abstract class JsonReader extends Reader
{
    public function execute($input)
    {
        $array = Json::decode($input, true);
     
        return $this->arrayToResource($array);
    }

    abstract protected function arrayToResource(Array $array);
}
