<?php
/*
 * (c) Ben Longden <ben@nocarrier.co.uk>
 */

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Exceptions\BadRequest;

use SimpleXMLElement;
use Exception;

class HALXmlFormatter extends Formatter
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
        $doc = new SimpleXMLElement('<resource></resource>');
        if (!is_null($uri = $resource->getUri())) {
            $doc->addAttribute('href', $uri);
        }
        $this->linksForXml($doc, $resource->getAllLinks());

        if ($data = $resource->getData()) {
            $this->arrayToXml($data, $doc);
        }

        foreach ($resource->getAllResources() as $rel => $resources) {
            $this->resourcesForXml($doc, $rel, $resources);
        }

        $dom = dom_import_simplexml($doc);
        if ($pretty) {
            $dom->ownerDocument->preserveWhiteSpace = false;
            $dom->ownerDocument->formatOutput = true;
        }

        return $dom->ownerDocument->saveXML();
    }

    protected function linksForXml(SimpleXmlElement $doc, Array $links)
    {
        foreach ($links as $rel => $links) {
            if (!is_array($links)) $links = [$links];

            foreach ($links as $link) {
                $element = $doc->addChild('link');
                $element->addAttribute('rel', $rel);
                $element->addAttribute('href', $link->getHref());

                foreach ($link->getAttributes() as $attribute => $value) {
                    $element->addAttribute($attribute, $value);
                }
            }
        }
    }

    protected function arrayToXml(array $data, SimpleXmlElement $element, $parent=null)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    if (count($value) > 0 && isset($value[0])) {
                        $this->arrayToXml($value, $element, $key);
                    } else {
                        $subnode = $element->addChild($key);
                        $this->arrayToXml($value, $subnode, $key);
                    }
                } else {
                    $subnode = $element->addChild($parent);
                    $this->arrayToXml($value, $subnode, $parent);
                }
            } else {
                if (!is_numeric($key)) {
                    if (substr($key, 0, 1) === '@') {
                        $element->addAttribute(substr($key, 1), $value);
                    } elseif ($key === 'value' and count($data) === 1) {
                        $element->{0} = $value;
                    } elseif (is_bool($value)) {
                        $element->addChild($key, intval($value));
                    } else {
                        $element->addChild($key, htmlspecialchars($value, ENT_QUOTES));
                    }
                } else {
                    $element->addChild($parent, htmlspecialchars($value, ENT_QUOTES));
                }
            }
        }
    }

    protected function resourcesForXml(SimpleXmlElement $doc, $rel, array $resources)
    {
        foreach ($resources as $resource) {

            $element = $doc->addChild('resource');
            $element->addAttribute('rel', $rel);

            if ($resource) {
                if (!is_null($uri = $resource->getURI())) {
                    $element->addAttribute('href', $uri);
                }

                $this->linksForXml($element, $resource->getAllLinks());

                foreach ($resource->getAllResources() as $innerRel => $innerRes) {
                    $this->resourcesForXml($element, $innerRel, $innerRes);
                }

                if ($data = $resource->getData()) {
                    $this->arrayToXml($data, $element);
                }
            }
        }
    }
}
