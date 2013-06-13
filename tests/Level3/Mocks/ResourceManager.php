<?php

namespace Level3\Mocks;
use Level3\ResourceManager as Level3ResourceManager;
use Level3\ResourceManager\DeleteInterface;
use Level3\ResourceManager\GetInterface;
use Level3\ResourceManager\PostInterface;
use Level3\ResourceManager\PutInterface;

/**
* Foo
*/
class ResourceManager 
    extends Level3ResourceManager 
    implements GetInterface, PostInterface, PutInterface, DeleteInterface
{
    public function getOne($id)
    {

    }
    
    public function get()
    {

    }

    public function delete($id)
    {

    }
    
    public function put($data)
    {

    }

    public function post($id, $data)
    {

    }
}