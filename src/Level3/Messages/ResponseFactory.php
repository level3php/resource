<?php

namespace Level3\Messages;

use Level3\Hal\Resource;
use Teapot\StatusCode;

class ResponseFactory
{
    public function create(Resource $resource = null, $statusCode = StatusCode::OK)
    {
        return new Response($resource, $statusCode);
    }
}