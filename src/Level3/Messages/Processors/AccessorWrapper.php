<?php

namespace Level3\Messages\Processors;

use Exception;
use Level3\Accessor;
use Level3\Messages\MessageProcessor;
use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\Request;
use Level3\Repository\Exception\BaseException;
use Teapot\StatusCode;

class AccessorWrapper implements RequestProcessor
{
    private $accessor;
    private $messageProcessor;

    public function __construct(Accessor $resourceAccessor, MessageProcessor $messageProcessor)
    {
        $this->accessor = $resourceAccessor;
        $this->messageProcessor = $messageProcessor;
    }

    public function find(Request $request)
    {
        try {
            $response = $this->findResources($request);
        } catch (\Exception $e) {
            $response = $this->messageProcessor->createErrorResponse($e);
        }

        return $response;
    }

    private function findResources(Request $request)
    {
        $key = $request->getKey();
        $resource = $this->accessor->find($key);
        return $this->messageProcessor->createOKResponse($request, $resource);
    }

    public function get(Request $request)
    {
        try {
            $response = $this->getResource($request);
        } catch (\Exception $e) {
            $response = $this->messageProcessor->createErrorResponse($e);
        }

        return $response;
    }

    private function getResource(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $resource = $this->accessor->get($key, $id);
        return $this->messageProcessor->createOKResponse($request, $resource);
    }

    public function post(Request $request)
    {
        try {
            $response = $this->modifyResource($request);
        } catch (\Exception $e) {
            $response = $this->messageProcessor->createErrorResponse($e);
        }

        return $response;
    }

    private function modifyResource(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $content = $this->messageProcessor->getRequestContentAsArray($request);
        $resource = $this->accessor->post($key, $id, $content);
        return $this->messageProcessor->createOKResponse($request, $resource);
    }

    public function put(Request $request)
    {
        try {
            $response = $this->createResource($request);
        } catch (\Exception $e) {
            $response = $this->messageProcessor->createErrorResponse($e);
        }

        return $response;
    }

    private function createResource(Request $request)
    {
        $key = $request->getKey();
        $content = $this->messageProcessor->getRequestContentAsArray($request);
        $resource = $this->accessor->put($key, $content);
        return $this->messageProcessor->createOKResponse($request, $resource);
    }

    public function delete(Request $request)
    {
        try {
            $response = $this->deleteResource($request);
        } catch (\Exception $e) {
            $response = $this->messageProcessor->createErrorResponse($e);
        }

        return $response;
    }

    private function deleteResource(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $this->accessor->delete($key, $id);
        return $this->messageProcessor->createOKResponse($request);
    }
}