<?php

namespace Level3\Hal;

class ResourceFactory 
{
    public function create($uri, array $data = array())
    {
        return new Resource($uri, $data);
    }
}