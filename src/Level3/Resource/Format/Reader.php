<?php

namespace Level3\Resource\Format;

use Level3\Resource\Resource;

abstract class Reader
{
    abstract public function execute($input);

    public function getContentType()
    {
        return static::CONTENT_TYPE;
    }
}
