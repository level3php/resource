<?php
namespace Level3;


abstract class Resource {
    private $uri;
    private $metadata = array();

    public function setURI($uri)
    {
        $this->uri = $uri;
    }

    public function setName($name)
    {
        $this->metadata['name'] = $name;
    }

    public function setDescription($desc)
    {
        $this->metadata['desc'] = $desc;
    }

    public function getURI()
    {
        return $this->uri;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}