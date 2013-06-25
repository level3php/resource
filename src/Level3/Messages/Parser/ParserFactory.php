<?php

namespace Level3\Messages\Parser;

class ParserFactory 
{
    const HEADER_APPLICATION_JSON = 'application/json';
    const HEADER_APPLICATION_XML = 'application/xml';

    public function createParser($format)
    {
        switch($format) {
            case self::HEADER_APPLICATION_XML:
                return new XmlParser();
            case self::HEADER_APPLICATION_JSON:
            default:
                return new JsonParser();
        }
    }
}