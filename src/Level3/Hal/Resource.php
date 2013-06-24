<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Hal;
use Level3\Hal\Format\XML;
use Level3\Hal\Format\JSON;

class Resource
{
    protected $uri;
    protected $data;
    protected $resources;
    protected $links;

    public function __construct($uri = null, array $data = array())
    {
        $this->uri = $uri;
        $this->data = $data;
        $this->resources = array();
        $this->links = array();
    }

    public function addLink($rel, Link $link)
    {
        $this->links[$rel][] = $link;
        return $this;
    }

    public function getLinks()
    {
        return $this->links;
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

    public function setData(Array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
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

    public function asJson($pretty = false)
    {
        $renderer = new JSON();
        return $renderer->to($this, $pretty);
    }

    public function asXml($pretty = false)
    {
        $renderer = new XML();
        return $renderer->to($this, $pretty);
    }
}
