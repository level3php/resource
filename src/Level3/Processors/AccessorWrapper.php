<?php

namespace Level3\Messages\Processors;

use Exception;
use Level3\Accessor;
use Level3\Hal\Formatter\FormatterFactory;
use Level3\Hal\Resource;
use Level3\Messages\Exceptions\NotAcceptable;
use Level3\Messages\MessageProcessor;
use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Level3\Repository\Exception\BaseException;
use Teapot\StatusCode;

class AccessorWrapper implements RequestProcessor
{
    private $accessor;
    private $responseFactory;
    private $parserFactory;

    public function __construct(
        Accessor $resourceAccessor,
        ResponseFactory $responseFactory,
        ParserFactory $parserFactory
    )
    {
        $this->accessor = $resourceAccessor;
        $this->responseFactory = $responseFactory;
        $this->parserFactory = $parserFactory;
    }

    public function find(Request $request)
    {
        $key = $request->getKey();
        $sort = $request->getSort();
        $range = $request->getRange();
        $criteria = $request->getCriteria();
        $parameters = $request->getParameters();
        
        $resource = $this->accessor->find(
            $key, $parameters, $sort, $range[0], $range[1], $criteria);
        
        return $this->createResponse($request, $resource);
    }

    public function get(Request $request)
    {
        $key = $request->getKey();
        $parameters = $request->getParameters();

        $resource = $this->accessor->get($key, $parameters);
        return $this->createResponse($request, $resource);
    }

    public function post(Request $request)
    {
        $key = $request->getKey();
        $parameters = $request->getParameters();
        $content = $this->getRequestContentAsArray($request);
        $resource = $this->accessor->post($key, $parameters, $content);
        return $this->createResponse($request, $resource);
    }

    public function put(Request $request)
    {
        $key = $request->getKey();
        $parameters = $request->getParameters();
        $content = $this->getRequestContentAsArray($request);
        $resource = $this->accessor->put($key, $parameters, $content);
        return $this->createResponse($request, $resource, StatusCode::CREATED);
    }

    public function delete(Request $request)
    {
        $key = $request->getKey();
        $parameters = $request->getParameters();
        $this->accessor->delete($key, $parameters);
        return $this->responseFactory->createFromDataAndStatusCode($request, array(), StatusCode::OK);
    }

    private function getRequestContentAsArray(Request $request)
    {
        $contentType = $request->getContentType();
        $parser = $this->parserFactory->create($contentType);
        $content = $parser->parse($request->getContent());
        return $content;
    }

    private function createResponse(Request $request, Resource $resource, $statusCode = StatusCode::OK)
    {
        return $this->responseFactory->create($request, $resource, $statusCode);
    }
}
