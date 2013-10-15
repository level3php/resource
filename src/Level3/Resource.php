<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3;

use Level3\Resource\Link;
use DateTime;
use InvalidArgumentException;

class Resource
{
    protected $id;
    protected $uri;
    protected $resources = array();
    protected $links = array();
    protected $data;
    protected $lastUpdate;
    protected $cache;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setURI($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getURI()
    {
        return $this->uri;
    }

    public function addLink($rel, Link $link)
    {
        $this->links[$rel][] = $link;

        return $this;
    }

    public function linkResource($rel, Resource $resource)
    {
        $link = $resource->getSelfLink();
        if (!$link) {
            throw new InvalidArgumentException(
                'This resource not contains a valid URI'
            );
        }

        $this->addLink($rel, $link);

        return $this;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getLinksByRel($rel)
    {
        if (isset($this->links[$rel])) {
            return $this->links[$rel];
        }

        return null;
    }

    public function addResource($rel, Resource $resource)
    {
        $this->resources[$rel][] = $resource;

        return $this;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getResourcesByRel($rel)
    {
        if (isset($this->resources[$rel])) {
            return $this->resources[$rel];
        }

        return null;
    }

    public function setData(Array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function addData($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getSelfLink()
    {
        if (!$this->uri) {
            return null;
        }

        return new Link($this->getURI());
    }

    public function setLastUpdate(DateTime $date)
    {
        $this->lastUpdate = $date;

        return $this;
    }

    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    public function setCache($secs)
    {
        $this->cache = $secs;

        return $this;
    }

    public function getCache()
    {
        return $this->cache;
    }
}
