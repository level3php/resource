<?php
namespace Level3;
use Hal\Resource;

abstract class ResourceManager {
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
        $rc = new \ReflectionClass(get_class($this));

        $description = substr($rc->getDocComment(), 3, -2);
        $description = trim(preg_replace('/\s*\*/', '', $description));
        return $description;
    }

    public function create($id)
    {
        if (!$this->hub) {
            throw new \RuntimeException('Set a ResourceHub before use create method.');
        }

        $uri = $this->hub->getURI($this->key, 'get', array('id' => $id));
        return new Resource($uri);
    }
}