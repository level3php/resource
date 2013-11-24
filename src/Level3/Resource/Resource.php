<?php

namespace Level3\Resource;

use Level3\Resource\Format\Writer;
use RuntimeException;
use InvalidArgumentException;
use DateTime;

class Resource
{
    protected $id;
    protected $title;
    protected $key;
    protected $relation;
    protected $uri;
    protected $resources = [];
    protected $linkedResources = [];
    protected $links = [];
    protected $data = [];
    protected $lastUpdate;
    protected $cache;
    protected $formatter;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRepositoryKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getRepositoryKey()
    {
        return $this->key;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
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

    public function setLink($rel, Link $link)
    {
        $this->links[$rel] = $link;

        return $this;
    }

    public function setLinks($rel, Array $links)
    {
        foreach ($links as $link) {
            if (!$link instanceof Link) {
                throw new InvalidArgumentException(
                    'Invalid array, must be []Link'
                );
            }
        }

        $this->links[$rel] = $links;

        return $this;
    }

    public function linkResource($rel, Resource $resource)
    {
        $this->trowExceptionIfNotLinkableResouce($resource);
        $this->linkedResources[$rel] = $resource;

        return $this;
    }

    public function linkResources($rel, Array $resources)
    {
        foreach ($resources as $resource) {
            $this->trowExceptionIfNotLinkableResouce($resource);
        }

        $this->linkedResources[$rel] = $resources;

        return $this;
    }

    protected function trowExceptionIfNotLinkableResouce(Resource $resource)
    {
        $link = $resource->getSelfLink();
        if (!$link) {
            throw new InvalidArgumentException(
                'This resource not contains a valid URI'
            );
        }
    }

    public function expandLinkedResourcesTree(Array $path)
    {
        if (count($path) == 1) {
            return $this->expandLinkedResources(end($path));
        }

        $rel = array_shift($path);
        $this->expandLinkedResources($rel);

        $resources = $this->getResources($rel);
        if (!$resources) {
            return;
        }

        if (!is_array($resources)) {
            $resources = [$resources];
        }

        foreach ($resources as $resource) {
            $resource->expandLinkedResourcesTree($path);
        }
    }

    public function expandLinkedResources($rel)
    {
        $resources = $this->getLinkedResources($rel);
        if (!$resources) {
            return;
        }

        if (!is_array($resources)) {
            $this->addResource($rel, $resources);
        } else {
            $this->addResources($rel, $resources);
        }
    }

    public function getAllLinkedResources()
    {
        return $this->linkedResources;
    }

    public function getLinkedResources($rel)
    {
        if (!isset($this->linkedResources[$rel])) {
            return null;
        }

        return $this->linkedResources[$rel];
    }

    public function getAllLinks()
    {
        return $this->links;
    }

    public function getLinks($rel)
    {
        if (isset($this->links[$rel])) {
            return $this->links[$rel];
        }

        return null;
    }

    public function addResource($rel, Resource $resource)
    {
        $this->resources[$rel] = $resource;

        return $this;
    }

    public function addResources($rel, Array $resources)
    {
        foreach ($resources as $resource) {
            if (!$resource instanceof Resource) {
                throw new InvalidArgumentException(
                    'Invalid array, must be []Resource'
                );
            }
        }

        $this->resources[$rel] = $resources;

        return $this;
    }

    public function getAllResources()
    {
        return $this->resources;
    }

    public function getResources($rel)
    {
        if (isset($this->resources[$rel])) {
            return $this->resources[$rel];
        }

        return null;
    }

    public function getSelfLink()
    {
        if (!$this->uri) {
            return null;
        }

        $link = new Link($this->getURI());

        if ($title = $this->getTitle()) {
            $link->setTitle($title);
        }

        if ($name = $this->getId()) {
            $link->setName($name);
        }

        return $link;
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
