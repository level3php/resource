<?php

namespace Level3;

use Hal\Resource;

class ResponseFactory
{
    public function createResponse($resource, $statusCode)
    {
        return new Response($resource, $statusCode);
    }
}