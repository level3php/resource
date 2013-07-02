<?php

namespace Level3\Messages\Parser;

class ParserFactory 
{
    const HEADER_APPLICATION_JSON = 'application/hal+json';
    const HEADER_APPLICATION_XML = 'application/hal+xml';

    public function create($format)
    {
        switch($format) {
            case self::HEADER_APPLICATION_XML:
                return new XmlParser();
            case self::HEADER_APPLICATION_JSON:
                return new JsonParser();
            default:
                return new ArrayParser();
        }
    }
}