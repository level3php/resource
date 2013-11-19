<?php

namespace Level3\Resource;

abstract class Formatter
{
    const CONTENT_TYPE_ANY = '*/*';

    protected $pretty;

    public function __construct($pretty = false) {
        $this->pretty = $pretty;
    }

    abstract public function toResponse(Resource $resource);

    abstract public function fromRequest($string);

    public function getContentType()
    {
        return static::CONTENT_TYPE;
    }
}
