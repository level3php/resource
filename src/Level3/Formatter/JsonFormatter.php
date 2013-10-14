<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 * (c) Ben Longden <ben@nocarrier.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Exceptions\BadRequest;

class JsonFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/hal+json';

    public function fromRequest($string)
    {
        if (strlen($string) == 0) {
            return Array();
        }
        
        $array = json_decode($string, true);

        if (!is_array($array)) {
            throw new BadRequest();
        }

        return $array;
    }

    public function toResponse(Resource $resource, $pretty = false)
    {
        $options = 0;
        if (version_compare(PHP_VERSION, '5.4.0') >= 0 and $pretty) {
            $options = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        }

        return json_encode($this->arrayForJson($resource), $options);
    }

    protected function linksForJson($uri, $links)
    {
        $data = array();
        if (!is_null($uri)) {
            $data['self'] = array('href' => $uri);
        }

        foreach($links as $rel => $links) {
            if (count($links) === 1 && $rel !== 'curies') {
                $data[$rel] = array('href' => $links[0]->getHref());
                foreach ($links[0]->getAttributes() as $attribute => $value) {
                    $data[$rel][$attribute] = $value;
                }
            } else {
                $data[$rel] = array();
                foreach ($links as $link) {
                    $item = array('href' => $link->getHref());
                    foreach ($link->getAttributes() as $attribute => $value) {
                        $item[$attribute] = $value;
                    }
                    $data[$rel][] = $item;
                }
            }
        }

        return $data;
    }

    protected function resourcesForJson($resources)
    {
        $data = array();

        foreach ($resources as $resource) {
            $res = $this->arrayForJson($resource);

            if(!empty($res)){
                $data[] = $res;
            }
        }

        return $data;
    }

    protected function stripAttributeMarker(array $data)
    {
        foreach ($data as $key => $value) {
            if (substr($key, 0, 5) == '@xml:') {
                $data[substr($key, 5)] = $value;
                unset ($data[$key]);
            } elseif (substr($key, 0, 1) == '@') {
                $data[substr($key, 1)] = $value;
                unset ($data[$key]);
            }

            if (is_array($value)) {
                $data[$key] = $this->stripAttributeMarker($value);
            }
        }

        return $data;
    }

    protected function arrayForJson(Resource $resource = null)
    {
        if (!$resource){
            return array();
        }

        $data = (array) $resource->getData();
        $data = $this->stripAttributeMarker($data);

        $links = $this->linksForJson($resource->getURI(), $resource->getLinks());
        if (count($links)) {
            $data['_links'] = $links;
        }

        foreach($resource->getResources() as $rel => $resources) {
            $data['_embedded'][$rel] = $this->resourcesForJson($resources);
        }

        return $data;
    }
}