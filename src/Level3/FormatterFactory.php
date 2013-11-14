<?php

namespace Level3;

use Level3\Formatter\SirenJsonFormatter;
use Level3\Formatter\HALJsonFormatter;
use Level3\Formatter\HALXmlFormatter;
use Level3\Exceptions\NotAcceptable;

class FormatterFactory
{
    public function create(array $contentTypes = [], $avoidNotAcceptable = false)
    {
        if (count($contentTypes) === 0) {
            return new HALJsonFormatter();
        }

        foreach ($contentTypes as $contentType) {
            switch ($contentType) {
                case SirenJsonFormatter::CONTENT_TYPE:
                    return new SirenJsonFormatter();
                case HALXmlFormatter::CONTENT_TYPE:
                    return new HALXmlFormatter();
                case HALJsonFormatter::CONTENT_TYPE:
                case Formatter::CONTENT_TYPE_ANY:
                    return new HALJsonFormatter();
            }
        }

        if ($avoidNotAcceptable) {
            return new HALJsonFormatter();
        }

        throw new NotAcceptable(sprintf('Content-Type not supported: %s', join(', ', $contentTypes)));
    }
}
