<?php

namespace Level3;
use Level3\Hal\Resource;

class ResponseFactory
{
    public function createResponse(Resource $resource, $statusCode)
    {
        return new Response($resource, $statusCode);
    }
}