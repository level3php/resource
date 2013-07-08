<?php

namespace Level3\Security\Authorization;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Teapot\StatusCode;

abstract class AbstractAuthorizationProcessor implements RequestProcessor
{
    protected $processor;
    protected $responseFactory;

    public function __construct(RequestProcessor $processor, ResponseFactory $responseFactory)
    {
        $this->processor = $processor;
        $this->responseFactory = $responseFactory;
    }

    public function find(Request $request)
    {
        return $this->processRequest($request, 'find');
    }

    public function get(Request $request)
    {
        return $this->processRequest($request, 'get');
    }

    public function post(Request $request)
    {
        return $this->processRequest($request, 'post');
    }

    public function put(Request $request)
    {
        return $this->processRequest($request, 'put');
    }

    public function delete(Request $request)
    {
        return $this->processRequest($request, 'delete');
    }

    private function processRequest(Request $request, $methodName)
    {
        if ($this->hasAccess($request, $methodName)) {
            return $this->processor->$methodName($request);
        }

        return $this->generateForbiddenResponse();
    }

    protected abstract function hasAccess(Request $request, $methodName);

    private function generateForbiddenResponse()
    {
        return $this->responseFactory->createFromDataAndStatusCode(array(), StatusCode::FORBIDDEN);
    }
}