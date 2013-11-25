<?php

namespace Level3\Resource\Format\Reader\HAL;

use Level3\Resource\Resource;
use Level3\Resource\Link;
use Level3\Resource\Format\Reader\JsonReader as BaseJsonReader;

class JsonReader extends BaseJsonReader
{
    const CONTENT_TYPE = 'application/hal+json';

    const RESOURCE_KEY_LINKS = '_links';
    const RESOURCE_KEY_EMBEDDED = '_embedded';
    const RESOURCE_LINK_SELF = 'self';

    const LINK_KEY_HREF = 'href';
    const LINK_KEY_TEMPLATED = 'templated';
    const LINK_KEY_NAME = 'name';
    const LINK_KEY_LANG = 'hreflang';
    const LINK_KEY_TITLE = 'title';

    protected function arrayToResource(Array $array)
    {
        return $this->doTransformEmbedded($array);
    }

    protected function transformMetadata(Resource $resource, Array $array)
    {
        if (isset($array[self::RESOURCE_KEY_LINKS])) {
            $this->transformLinks($resource, $array[self::RESOURCE_KEY_LINKS]);
        }

        if (isset($array[self::RESOURCE_KEY_EMBEDDED])) {
            $this->transformEmbeddeds($resource, $array[self::RESOURCE_KEY_EMBEDDED]);
        }
    }

    protected function transformLinks(Resource $resource, Array $linksByRel)
    {
        foreach ($linksByRel as $rel => $array) {
            if ($rel == self::RESOURCE_LINK_SELF) {
                $this->transformSelfLink($resource, $array);
                continue;
            }

            if ($this->isAssocArray($array)) {
                $link = $this->doTransformLink($array);
                $resource->setLink($rel, $link);
            } else {
                $links = $this->doTransformLinks($array);
                $resource->setLinks($rel, $links);
            }
        }
    }

    protected function transformSelfLink(Resource $resource, Array $array)
    {
        if (isset($array[self::LINK_KEY_HREF])) {
            $resource->setURI($array[self::LINK_KEY_HREF]);
        }

        if (isset($array[self::LINK_KEY_TITLE])) {
            $resource->setTitle($array[self::LINK_KEY_TITLE]);
        }      
    }

    protected function doTransformLinks(Array $array)
    {
        $links = [];
        foreach ($array as $link) {
            $links[] = $this->doTransformLink($link);
        }

        return $links;
    }

    protected function doTransformLink(Array $array)
    {
        $link = new Link();
        if (isset($array[self::LINK_KEY_HREF])) {
            $link->setHref($array[self::LINK_KEY_HREF]);
        }

        if (isset($array[self::LINK_KEY_TEMPLATED])) {
            $link->setTemplated($array[self::LINK_KEY_TEMPLATED]);
        }

        if (isset($array[self::LINK_KEY_NAME])) {
            $link->setName($array[self::LINK_KEY_NAME]);
        }

        if (isset($array[self::LINK_KEY_LANG])) {
            $link->setLang($array[self::LINK_KEY_LANG]);
        }

        if (isset($array[self::LINK_KEY_TITLE])) {
            $link->setTitle($array[self::LINK_KEY_TITLE]);
        }      

        return $link;
    }

    protected function transformEmbeddeds(Resource $resource, Array $embeddedsByRel)
    {
        foreach ($embeddedsByRel as $rel => $array) {
            if ($this->isAssocArray($array)) {
                $embedded = $this->doTransformEmbedded($array);
                $resource->addResource($rel, $embedded);
            } else {
                $embeddeds = $this->doTransformEmbeddeds($array);
                $resource->addResources($rel, $embeddeds);
            }
        }
    }

    protected function doTransformEmbeddeds(Array $array)
    {
        $embeddeds = [];
        foreach ($array as $embedded) {
            $embeddeds[] = $this->doTransformEmbedded($embedded);
        }

        return $embeddeds;
    }

    protected function doTransformEmbedded(Array $array)
    {
        $resource = new Resource();
        foreach ($array as $key => $value) {
            if (substr($key, 0, 1) !== '_') {
                $resource->addData($key, $value);
            }
        }

        $this->transformMetadata($resource, $array);

        return $resource;
    }

    private function isAssocArray($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}