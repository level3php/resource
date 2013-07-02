<?php

namespace Level3\Hal\Formatter;

use Level3\Hal\Formatter\JsonFormatter;

class FormatterFactory
{
    const CONTENT_TYPE_APPLICATION_HAL_JSON = 'application/hal+json';
    const CONTENT_TYPE_APPLICATION_HAL_XML = 'application/hal+xml';

    public function create($format)
    {
        switch($format){
            case self::CONTENT_TYPE_APPLICATION_HAL_XML:
                return new XmlFormatter();
            case self::CONTENT_TYPE_APPLICATION_HAL_JSON:
            default:
                return new JsonFormatter();
        }
    }
}