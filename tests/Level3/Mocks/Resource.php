<?php

namespace Level3\Mocks;
use Level3\Resources\DeleteInterface;
use Level3\Resources\GetInterface;
use Level3\Resources\PostInterface;
use Level3\Resources\PutInterface;

class Resource implements GetInterface, PostInterface, PutInterface, DeleteInterface
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