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
use Hal\Resource;

abstract class ResourceRepository
{
    private $hub;
    private $key;

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setHub(ResourceHub $hub)
    {
        $this->hub = $hub;
    }

    public function getHub()
    {
        return $this->hub;
    }

    public function getDescription()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));

        $description = substr($reflectionClass->getDocComment(), 3, -2);
        $description = trim(preg_replace('/\s*\*/', '', $description));
        return $description;
    }

    public function create($id)
    {
        if (!$this->hub) {
            throw new \RuntimeException('Set a ResourceHub before using create method.');
        }

        $uri = $this->hub->getURI($this->key, 'get', array('id' => $id));
        

        $resource = new Resource($uri);
        $resource->setData($this->resource($id));

        return $resource;
    }

    abstract protected function resource($id);
}