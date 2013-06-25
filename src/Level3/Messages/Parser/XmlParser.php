<?php

namespace Level3\Messages\Parser;

class XmlParser implements Parser
{
    public function parse($content)
    {
        return new \SimpleXMLElement($content);
    }
}