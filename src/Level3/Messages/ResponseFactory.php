<?php

namespace Level3\Messages;

use Level3\Hal\Resource;
use Level3\Hal\ResourceFactory;
use Teapot\StatusCode;

class ResponseFactory
{
    private $resourceFactory;

    public function __construct(ResourceFactory $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function create(Resource $resource = null, $statusCode = StatusCode::OK)
    {
        return new Response($resource, $statusCode);
    }

    public function createFromDataAndStatusCode(array $data, $statusCode)
    {
        $resource = $this->resourceFactory->create(null, $data);
        return $this->create($resource, $statusCode);
    }
}