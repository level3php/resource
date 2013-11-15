<?php

namespace Level3\Resource;

use Level3\Resource\Formatter\Siren;
use Level3\Resource\Formatter\HAL;
use Level3\Exceptions\NotAcceptable;

class FormatterFactory
{
    public function create(Array $contentTypes = [], $avoidNotAcceptable = false)
    {
        if (count($contentTypes) === 0) {
            return new HAL\JsonFormatter();
        }

        foreach ($contentTypes as $contentType) {
            switch ($contentType) {
                case Siren\JsonFormatter::CONTENT_TYPE:
                    return new Siren\JsonFormatter();
                case HAL\XMLFormatter::CONTENT_TYPE:
                    return new HAL\XMLFormatter();
                case HAL\JsonFormatter::CONTENT_TYPE:
                case Formatter::CONTENT_TYPE_ANY:
                    return new HAL\JsonFormatter();
            }
        }

        if ($avoidNotAcceptable) {
            return new HAL\JsonFormatter();
        }

        throw new NotAcceptable(sprintf('Content-Type not supported: %s', join(', ', $contentTypes)));
    }
}
