<?php

namespace Level3;
use Level3\Hal\Resource;
use Teapot\StatusCode;

class ResponseFactory
{
    public function createResponse(Resource $resource = null, $statusCode = StatusCode::OK)
    {
        return new Response($resource, $statusCode);
    }
}