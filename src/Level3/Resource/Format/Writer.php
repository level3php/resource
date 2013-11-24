<?php

namespace Level3\Resource\Format;

use Level3\Resource\Resource;

abstract class Writer
{
    const CONTENT_TYPE_ANY = '*/*';

    protected $pretty;

    public function __construct($pretty = false) {
        $this->pretty = $pretty;
    }

    abstract public function execute(Resource $resource);

    public function getContentType()
    {
        return static::CONTENT_TYPE;
    }
}
