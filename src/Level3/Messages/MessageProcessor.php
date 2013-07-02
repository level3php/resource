<?php

namespace Level3\Messages;


use Level3\Hal\Resource;
use Level3\Messages\Request;

class MessageProcessor
{
    private $requestParser;
    private $responseGenerator;

    public function __construct(RequestParser $requestParser, ResponseGenerator $responseGenerator)
    {
        $this->requestParser = $requestParser;
        $this->responseGenerator = $responseGenerator;
    }

    public function generateErrorResponse(\Exception $e)
    {
        return $this->responseGenerator->generateErrorResponse($e);
    }

    public function generateOKResponse(Request $request, Resource $resource)
    {
        return $this->responseGenerator->generateOKResponse($request, $resource);
    }

    public function generateDeletedResponse()
    {
        return $this->responseGenerator->generateDeletedResponse();
    }

    public function getRequestContentAsArray(Request $request)
    {
        return $this->requestParser->getRequestContentAsArray($request);
    }

    public function getRequestRange(Request $request)
    {
        return $this->requestParser->getRequestRange($request);
    }
}