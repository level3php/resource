<?php

namespace Level3\Messages;

use Hal\Resource;

class ResponseFactory
{
    public function createResponse($resource, $statusCode)
    {
        return new Response($resource, $statusCode);
    }
}