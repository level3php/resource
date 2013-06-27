<?php

namespace Level3\Messages;

use Level3\Hal\Resource;
use Level3\Messages\Formatter\FormatterFactory;
use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;

class MessageProcessor
{
    const HEADER_CONTENT_TYPE = 'content-type';
    const DEFAULT_CONTENT_TYPE = 'application/hal+json';
    const JSON_CONTENT_TYPE = 'application/hal+json';
    const XML_CONTENT_TYPE = 'application/hal+xml';

    private $responseFactory;
    private $parserFactory;
    private $formatterFactory;

    public function __construct(ResponseFactory $responseFactory, ParserFactory $parserFactory, FormatterFactory $formatterFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->parserFactory = $parserFactory;
        $this->formatterFactory = $formatterFactory;
    }

    public function createErrorResponse($status, $message = '')
    {
        return $this->responseFactory->create(null, $status);
    }

    public function createOKResponse(Request $request, Resource $resource = null)
    {
        return $this->responseFactory->create($resource, StatusCode::OK);
    }

    public function createCreatedResponse(Request $request, Resource $resource)
    {
        return $this->responseFactory->create($resource, StatusCode::CREATED);
    }

    protected function getResponseFormatter(Request $request)
    {
        try {
            return $this->getFirstAcceptedFormat($request);
        } catch (HeaderNotFound $e) {
            return $this->getDefaultAcceptedFormat();
        }
    }

    protected function getDefaultAcceptedFormat()
    {
        return self::DEFAULT_CONTENT_TYPE;
    }

    protected function getFirstAcceptedFormat(Request $request)
    {
        $acceptHeader = $request->getHeader(self::HEADER_ACCEPT);
        $acceptTypes = explode(self::ACCEPT_DELIMITER, $acceptHeader);

        foreach ($acceptTypes as $acceptType) {
            $acceptType = trim($acceptType);
            if ($this->isAccepted($acceptType))
                return $acceptType;
        }
    }

    private function isAccepted($contentType)
    {

    }

    public function getRequestContentAsArray(Request $request)
    {
        $contentType = $request->getHeader(self::HEADER_CONTENT_TYPE);
        $parser = $this->parserFactory->create($contentType);

        return $parser->parse($request->getContent());
    }
}