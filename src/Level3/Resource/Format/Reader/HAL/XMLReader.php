<?php

namespace Level3\Resource\Format\Reader\HAL;

use Level3\Resource\Resource;
use Level3\Resource\Link;
use Level3\Resource\Format\Reader\XMLReader as BaseXMLReader;
use UnexpectedValueException;

class XMLReader extends BaseXMLReader
{
    const CONTENT_TYPE = 'application/hal+xml';

    const RESOURCE_NAME = 'resource';
    const LINK_NAME = 'link';
    const LINK_KEY_REL = 'rel';

    const LINK_KEY_HREF = 'href';
    const LINK_KEY_TEMPLATED = 'templated';
    const LINK_KEY_NAME = 'name';
    const LINK_KEY_LANG = 'hreflang';
    const LINK_KEY_TITLE = 'title';

    protected function arrayToResource(Array $array)
    {
        if (count($array) != 1 || !$this->nodeIsResource(end($array))) {
            throw new UnexpectedValueException('Expencted only one resource node');
        }

        return $this->transformEmbedded(end($array));
    }

    private function nodeIsResource(Array $array)
    {
        return $array['name'] == self::RESOURCE_NAME;
    }

    private function nodeIsLink(Array $array)
    {
        return $array['name'] == self::LINK_NAME;
    }


    protected function transformLink(Array $node)
    {
        $link = new Link();
        $attributes = $node['attributes'];

        if (isset($attributes[self::LINK_KEY_HREF])) {
            $link->setHref($attributes[self::LINK_KEY_HREF]);
        }

        if (isset($attributes[self::LINK_KEY_TEMPLATED])) {
            $link->setTemplated($attributes[self::LINK_KEY_TEMPLATED]);
        }

        if (isset($attributes[self::LINK_KEY_NAME])) {
            $link->setName($attributes[self::LINK_KEY_NAME]);
        }

        if (isset($attributes[self::LINK_KEY_LANG])) {
            $link->setLang($attributes[self::LINK_KEY_LANG]);
        }

        if (isset($attributes[self::LINK_KEY_TITLE])) {
            $link->setTitle($attributes[self::LINK_KEY_TITLE]);
        }      

        return $link;
    }

    protected function transformEmbedded(Array $array)
    {
        $resource = new Resource();
        if (isset($array['attributes'][self::LINK_KEY_HREF])) {
            $resource->setURI($array['attributes'][self::LINK_KEY_HREF]);
        }

        if (isset($array['attributes'][self::LINK_KEY_TITLE])) {
            $resource->setTitle($array['attributes'][self::LINK_KEY_TITLE]);
        }   

        $embeddeds = [];
        $links = [];
        $data = [];

        foreach ($array['values'] as $node) {
            if ($this->nodeIsResource($node)) {
                $embeddeds[$node['attributes']['rel']][] = $this->transformEmbedded($node);
            } else if ($this->nodeIsLink($node)) {
                $links[$node['attributes']['rel']][] = $this->transformLink($node);
            } else {

                if (is_array($node['values'])) {
                    $toSave = [];
                    foreach ($node['values'] as $value) {
                        $toSave[$value['name']][] = $value['values'];
                    }
                } else {
                    $toSave = $node['values'];
                }

                $data[$node['name']][] = $toSave;
            }
        }

        $resource->setData($data);

        foreach ($links as $rel => $link) {
            $resource->setLinks($rel, $link);
        }

        foreach ($embeddeds as $rel => $embedded) {
            $resource->addResources($rel, $embedded);
        }

        return $resource;
    }

    private function isAssocArray($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
