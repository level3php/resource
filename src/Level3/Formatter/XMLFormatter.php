<?php

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Resource\Link;
use Level3\Exceptions\BadRequest;

use Exception;
use XMLWriter;
use SimpleXMLElement;

abstract class XMLFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/hal+xml';

    public function fromRequest($string)
    {
        if (strlen($string) == 0) {
            return [];
        }

        try {
            return $this->xmlToArray(new SimpleXMLElement($string));
        } catch (Exception $e) {
            throw new BadRequest();
        }
    }

    protected function xmlToArray(SimpleXMLElement $xml)
    {

        $data = (array) $xml;
        foreach ($data as $key => &$value) {
            if ($value instanceOf SimpleXMLElement) {
                $value = $this->xmlToArray($value);
            }
        }

        return $data;
    }

    public function toResponse(Resource $resource, $pretty = false)
    {
        $writer = new XMLWriter;
        $writer->openMemory();        
        $writer->setIndentString('  ');
        $writer->setIndent($pretty);
        $writer->startDocument('1.0'); 

        $this->resourceToArray($writer, $resource);

        return $writer->outputMemory();
    }

    abstract protected function resourceToArray(XMLWriter $writer, Resource $resource, $rel = null);
}
