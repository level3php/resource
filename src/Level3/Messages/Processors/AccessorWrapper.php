<?php

namespace Level3\Messages\Processors;

use Level3\Accessor;
use Level3\Messages\Request;
use Teapot\StatusCode;
use Level3\Repository\Exception\BaseException;
use Level3\Hal\Resource;
use Level3\Messages\ResponseFactory;
use Exception;

class AccessorWrapper implements RequestProcessor
{
    protected $accessor;
    protected $responseFactory;

    public function __construct(Accessor $resourceAccessor, ResponseFactory $responseFactory)
    {
        $this->accessor = $resourceAccessor;
        $this->responseFactory = $responseFactory;
    }

    public function find(Request $request)
    {
        $key = $request->getKey();

        try {
            return $this->findResources($key);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function findResources($key)
    {
        $resource = $this->accessor->find($key);
        return $this->createOKResponse($resource);
    }



    public function get(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();

        try {
            return $this->getResource($key, $id);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function getResource($key, $id)
    {
        $resource = $this->accessor->get($key, $id);
        return $this->createOKResponse($resource);
    }

    public function post(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $content = $request->getContent();

        try {
            return $this->modifyResource($key, $id, $content);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function modifyResource($key, $id, $content)
    {
        $resource = $this->accessor->post($key, $id, $content);
        return $this->createOKResponse($resource);
    }

    public function put(Request $request)
    {
        $key = $request->getKey();
        $content = $request->getContent();

        try {
            return $this->createResource($key, $content);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function createResource($key, $content)
    {
        $resource = $this->accessor->put($key, $content);
        return $this->createCreatedResponse($resource);
    }

    public function delete(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();

        try {
            return $this->deleteResource($key, $id);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function deleteResource($key, $id)
    {
        $this->accessor->delete($key, $id);
        return $this->createOKResponse(null);
    }

    private function createErrorResponse($status)
    {
        return $this->responseFactory->createResponse(null, $status);
    }

    private function createOKResponse(Resource $resource = null)
    {
        return $this->responseFactory->createResponse($resource, StatusCode::OK);
    }

    private function createCreatedResponse(Resource $resource)
    {
        return $this->responseFactory->createResponse($resource, StatusCode::CREATED);
    }
}
