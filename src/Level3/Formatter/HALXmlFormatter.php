<?php
/*
 * (c) Ben Longden <ben@nocarrier.co.uk>
 */

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Resource\Link;
use Level3\Exceptions\BadRequest;

use Sabre\XML;
use XMLWriter;
use Exception;

class HALXmlFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/hal+xml';

    public function fromRequest($string)
    {
 /*
$reader = new XML\Reader();
$reader->xml($string);

$output = $reader->parse();
print_r($output);
exit();
        var_dump($string);*/
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

    protected function resourceToArray(XMLWriter $writer, Resource $resource, $rel = null)
    {
        $writer->startElement('resource'); 
        if ($rel) {
            $this->addAttribute($writer, 'rel', $rel);
        }

        if ($self = $resource->getSelfLink()) {
            $this->addAttribute($writer, 'href', $self->getHref());
        }

        $this->transformLinks($writer, $resource);
        $this->transformData($writer, $resource);
        $this->transformResources($writer, $resource);
        $this->transformLinkedResources($writer, $resource);

        $writer->endElement(); 
    }

    protected function transformLinks(XMLWriter $writer, Resource $resource)
    {
        foreach ($resource->getAllLinks() as $rel => $links) {
            if (!is_array($links)) {
                $links = [$links];
            }

            foreach ($links as $link) {
                $this->doTransformLink($writer, $rel, $link);
            }
        }
    }

    protected function transformLinkedResources(XMLWriter $writer, Resource $resource)
    {
        foreach ($resource->getAllLinkedResources() as $rel => $resources) {
            if (!is_array($resources)) {
                $resources = [$resources];
            }

            foreach ($resources as $resource) {
                $this->doTransformLink($writer, $rel, $resource->getSelfLink());
            }
        }
    }

    private function doTransformLink(XMLWriter $writer, $rel, Link $link)
    {
        $writer->startElement('link'); 
        $this->addAttribute($writer, 'rel', $rel);

        foreach ($link->toArray() as $name => $value) {
            $this->addAttribute($writer, $name, $value);
        }

        $writer->endElement();
    }

    private function addAttribute(XMLWriter $writer, $name, $value)
    {
        return $writer->writeAttribute($name, $value);
    }

    protected function transformData(XMLWriter $writer, Resource $resource)
    {
        foreach ($resource->getData() as $name => $value) {
            $this->addValue($writer, $name, $value);
        }

        return;
    }

    private function addValue(XMLWriter $writer, $name, $value)
    {
        if (!is_array($value)) {
            return $this->doWriteString($writer, $name, $value);
        }

        reset($value);
        if (is_numeric(key($value))) {
            return $this->doWriteArray($writer, $name, $value);
        } else {
            return $this->doWriteAssocArray($writer, $name, $value);
        }
    }

    private function doWriteString(XMLWriter $writer, $name, $value)
    {
        $writer->writeElement($name, $value); 
    }

    private function doWriteArray(XMLWriter $writer, $name, Array $array)
    {
        foreach ($array as $childName => $childValue) {
           $this->addValue($writer, $name, $childValue);
        }        
    }

    private function doWriteAssocArray(XMLWriter $writer, $name, Array $array)
    {
        $writer->startElement($name); 
        foreach ($array as $childName => $childValue) {
           $this->addValue($writer, $childName, $childValue);
        }        

        $writer->endElement(); 
    }

    protected function transformResources(XMLWriter $writer, Resource $resource)
    {
        $embedded = [];
        foreach ($resource->getAllResources() as $rel => $resources) {
            if (!is_array($resources)) {
                $resources = [$resources];
            }

            foreach ($resources as $resource) {
                $this->resourceToArray($writer, $resource, $rel);
            }
        }
    }

}
