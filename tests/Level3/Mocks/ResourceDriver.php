<?php

namespace Level3\Mocks;
use Level3\ResourceDriver as Level3ResourceDriver;
use Level3\ResourceDriver\DeleteInterface;
use Level3\ResourceDriver\GetInterface;
use Level3\ResourceDriver\PostInterface;
use Level3\ResourceDriver\PutInterface;

class ResourceDriver 
    extends Level3ResourceDriver 
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