<?php

namespace Level3;

use Level3\Exceptions\NotFound;
use Level3\Messages\Response;
use Level3\Messages\Request;

use Teapot\StatusCode;
use RuntimeException;

class Processor
{
    private $level3;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function find(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();
        $filters = $request->getFilters();
        
        $resource = $this->getRepository($key)->find($attributes, $filters);
        
        return $this->createResponse($request, $resource);
    }

    public function get(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();

        $resource = $this->getRepository($key)->get($attributes);

        return $this->createResponse($request, $resource);
    }

    public function post(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();
        $content = $request->getContent();

        $resource = $this->getRepository($key)->post($attributes, $content);

        return $this->createResponse($request, $resource, StatusCode::CREATED);
    }

    public function patch(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();
        $content = $request->getContent();

        $resource = $this->getRepository($key)->patch($attributes, $content);

        return $this->createResponse($request, $resource);
    }

    public function put(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();
        $content = $request->getContent();

        $resource = $this->getRepository($key)->put($attributes, $content);

        return $this->createResponse($request, $resource);
    }

    public function delete(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();

        $this->getRepository($key)->delete($attributes);

        return $this->createEmptyResponse();
    }

    protected function createEmptyResponse($statusCode = StatusCode::NO_CONTENT)
    {
        $response = new Response();
        $response->setStatusCode($statusCode);

        return $response;
    }

    protected function createResponse(Request $request, Resource $resource, $statusCode = StatusCode::OK)
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->setResource($resource);
        $response->setFormatter($request->getFormatter());

        return $response;
    }

    protected function getRepository($key)
    {        
        try {
            return $this->level3->getRepository($key);
        } catch (RuntimeException $e) {
            throw new NotFound();
        }
    }
}
