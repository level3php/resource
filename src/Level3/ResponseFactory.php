<?php

namespace Level3;

use Hal\AbstractHal;

class ResponseFactory
{
    public function createResponse($HalResource, $statusCode)
    {
        return new Response($HalResource, $statusCode);
    }
}