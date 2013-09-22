<?php

namespace Level3;

use Level3\Resource\FormatterFactory;
use Level3\Messages\Exceptions\NotAcceptable;
use Level3\Messages\Response;
use Level3\Messages\Request;

use Teapot\StatusCode;
use Exception;

class AccessorWrapper implements RequestProcessor
{
    private $level3;
    private $formatter;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
        $this->formatterFactory = new FormatterFactory();
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
        $content = $request->getContentParsed();

        $resource = $this->getRepository($key)->post($attributes, $content);

        return $this->createResponse($request, $resource);
    }

    public function put(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();
        $content = $request->getContentParsed();

        $resource = $this->getRepository($key)->put($attributes, $content);

        return $this->createResponse($request, $resource, StatusCode::CREATED);
    }

    public function delete(Request $request)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();

        $this->getRepository($key)->delete($attributes);

        return $this->responseFactory->createFromDataAndStatusCode($request, array(), StatusCode::OK);
    }

    private function createResponse(Request $request, Resource $resource, $statusCode = StatusCode::OK)
    {
        $response = new Response($request, $resource, $statusCode);
        $response->setResource($resource);
        $response->setFormatter($request->getFormatter());

        return $response;
    }

    protected function getRepository($key)
    {
        return $this->level3->getRepository($key);
    }
}
