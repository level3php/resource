<?php

namespace Level3;

use Level3\Resource\Resource;
use Level3\Messages\Parameters;
use ReflectionClass;

abstract class Repository
{
    private $repositoryKey;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function getLevel3()
    {
        return $this->level3;
    }

    public function setKey($repositoryKey)
    {
        $this->repositoryKey = $repositoryKey;
    }

    public function getKey()
    {
        return $this->repositoryKey;
    }

    public function getURI(Parameters $attributes = null, $method = null)
    {
        $key = $this->getKey();

        return $this->level3->getURI($key, $method, $attributes);
    }

    public function getDescription()
    {
        $reflectionClass = new ReflectionClass(get_class($this));

        $description = substr($reflectionClass->getDocComment(), 3, -2);
        $description = trim(preg_replace('/\s*\*/', '', $description));

        return $description;
    }

    public function createResource(Parameters $attributes = null)
    {
        $uri = $this->getURI($attributes);

        $resource = new Resource();
        $resource->setURI($uri);

        return $resource;
    }
}
