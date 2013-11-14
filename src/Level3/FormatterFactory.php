<?php

namespace Level3;

use Level3\Formatter\JsonFormatter;
use Level3\Formatter\XmlFormatter;
use Level3\Exceptions\NotAcceptable;

class FormatterFactory
{
    public function create(array $contentTypes = [], $avoidNotAcceptable = false)
    {
        if (count($contentTypes) === 0) {
            return new JsonFormatter();
        }

        foreach ($contentTypes as $contentType) {
            switch ($contentType) {
                case XmlFormatter::CONTENT_TYPE:
                    return new XmlFormatter();
                case JsonFormatter::CONTENT_TYPE:
                case Formatter::CONTENT_TYPE_ANY:
                    return new JsonFormatter();
            }
        }

        if ($avoidNotAcceptable) {
            return new JsonFormatter();
        }

        throw new NotAcceptable(sprintf('Content-Type not supported: %s', join(', ', $contentTypes)));
    }
}
