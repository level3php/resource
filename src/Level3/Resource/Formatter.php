<?php

namespace Level3\Resource;

abstract class Formatter
{
    const CONTENT_TYPE_ANY = '*/*';

    abstract public function toResponse(Resource $resource, $pretty = false);

    abstract public function fromRequest($string);

    public function getContentType()
    {
        return static::CONTENT_TYPE;
    }
}
