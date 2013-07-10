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
    private $formatterFactory;
    private $parserFactory;
    private $isDebugEnabled = false;

    public function __construct(
        Accessor $resourceAccessor,
        ResponseFactory $responseFactory,
        FormatterFactory $formatterFactory,
        ParserFactory $parserFactory
    )
    {
        $this->accessor = $resourceAccessor;
        $this->responseFactory = $responseFactory;
        $this->formatterFactory = $formatterFactory;
        $this->parserFactory = $parserFactory;
    }

    public function enableDebug()
    {
        $this->isDebugEnabled = true;
    }

    public function disableDebug()
    {
        $this->isDebugEnabled = false;
    }

    public function find(Request $request)
    {
        return $this->processRequest($request, 'findResources');
    }

    public function get(Request $request)
    {
        return $this->processRequest($request, 'getResource');
    }

    public function post(Request $request)
    {
        return $this->processRequest($request, 'modifyResource');
    }

    public function put(Request $request)
    {
        return $this->processRequest($request, 'createResource');
    }

    public function delete(Request $request)
    {
        return $this->processRequest($request, 'deleteResource');
    }

    protected function processRequest(Request $request, $method)
    {
        try {
            $response = $this->$method($request);
        } catch (BaseException $e) {
            $response = $this->generateSpecificExceptionResponse($e);
        } catch (\Exception $e) {
            $response = $this->generateGenericExceptionResponse($e);
        }

        return $response;
    }

    private function generateSpecificExceptionResponse(BaseException $exception)
    {
        return $this->responseFactory->createFromDataAndStatusCode(array(), $exception->getCode());
    }

    private function generateGenericExceptionResponse(\Exception $exception)
    {
        $data = array('code' => StatusCode::INTERNAL_SERVER_ERROR);
        if ($this->isDebugEnabled) {
            $data['message'] = $exception->getMessage();
            $data['stackTrace'] = $exception->getTrace();
        }

        return $this->responseFactory->createFromDataAndStatusCode($data, StatusCode::INTERNAL_SERVER_ERROR);
    }

    private function findResources(Request $request)
    {
        $key = $request->getKey();
        $range = $request->getRange();
        $resource = $this->accessor->find($key, $range[0], $range[1]);
        return $this->createResponse($request, $resource);
    }

    private function getResource(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $resource = $this->accessor->get($key, $id);
        return $this->createResponse($request, $resource);
    }

    private function modifyResource(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $content = $this->getRequestContentAsArray($request);
        $resource = $this->accessor->post($key, $id, $content);
        return $this->createResponse($request, $resource);
    }

    private function createResource(Request $request)
    {
        $key = $request->getKey();
        $content = $this->getRequestContentAsArray($request);
        $resource = $this->accessor->put($key, $content);
        return $this->createResponse($request, $resource, StatusCode::CREATED);
    }

    private function getRequestContentAsArray(Request $request)
    {
        $contentType = $request->getContentType();
        $parser = $this->parserFactory->create($contentType);
        $content = $parser->parse($request->getContent());
        return $content;
    }

    private function deleteResource(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $this->accessor->delete($key, $id);
        return $this->createDeletedResponse();
    }

    private function createDeletedResponse()
    {
        return $this->responseFactory->createFromDataAndStatusCode(array(), StatusCode::OK);
    }

    private function createResponse(Request $request, Resource $resource, $statusCode = StatusCode::OK)
    {
        try {
            $response = $this->prepareResponse($request, $resource, $statusCode);
        } catch (NotAcceptable $e) {
            $response = $this->responseFactory->createFromDataAndStatusCode(array(), StatusCode::NOT_ACCEPTABLE);
        }

        return $response;
    }

    private function prepareResponse(Request $request, Resource $resource, $statusCode = StatusCode::OK)
    {

        $this->setResourceFormatter($request, $resource);
        $response = $this->responseFactory->create($resource, $statusCode);
        $response->prepare($request);
        return $response;
    }

    private function setResourceFormatter(Request $request, Resource $resource)
    {
        $contentTypes = $request->getAcceptableContentTypes();
        $formatter = $this->formatterFactory->create($contentTypes);
        $resource->setFormatter($formatter);
    }
}
