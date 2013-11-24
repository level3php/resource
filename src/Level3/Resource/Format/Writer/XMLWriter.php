<?php

namespace Level3\Resource\Format\Writer;

use Level3\Resource\Format\Writer;
use Level3\Resource\Resource;

use Exception;
use XMLWriter as BasicXMLWriter;
use SimpleXMLElement;

abstract class XMLWriter extends Writer
{
    public function execute(Resource $resource)
    {
        $writer = new BasicXMLWriter;
        $writer->openMemory();
        $writer->setIndentString('  ');
        $writer->setIndent($this->pretty);
        $writer->startDocument('1.0');

        $this->resourceToArray($writer, $resource);

        return $writer->outputMemory();
    }

    abstract protected function resourceToArray(BasicXMLWriter $writer, Resource $resource, $rel = null);
}
