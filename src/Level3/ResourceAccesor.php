<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3;

use Level3\ResourceRepository\Exception\BaseException;
use Teapot\StatusCode;

class ResourceAccesor
{
    private $hub;
    private $responseFactory;

    public function __construct(ResourceHub $hub, ResponseFactory $responseFactory)
    {
        $this->hub = $hub;
        $this->responseFactory = $responseFactory;
    }

    public function find($key)
    {
        try {
            return $this->findResource($key);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function findResource($key)
    {
        $resourceRepository = $this->hub[$key];
        $result = $resourceRepository->find();
        return $this->createOKResponse($result);
    }

    public function get($key, $id)
    {
        try {
            return $this->getResource($key, $id);
        } catch (BaseExcption $e) {
            $status = $e->getCode();
        } catch (Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function getResource($key, $id)
    {
        $resource = $this->hub[$key];
        $result = $resource->get($id);
        return $this->responseFactory->createResponse($result, StatusCode::OK);
    }

    public function post($key, $id, Array $data)
    {
        try {
            return $this->postDataForResourceWithKeyAndId($data, $key, $id);
        } catch (BaseExcption $e) {
            $status = $e->getCode();
        } catch (Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        $this->createErrorResponse($status);
    }

    private function postDataForResourceWithKeyAndId(Array $data, $key, $id)
    {
        $resource = $this->hub[$key];
        $resource->post($id, $data);
        $value = $resource->getOne($id);
        return $this->createOKResponse($value);
    }

    public function put($key, Array $data)
    {
        try {
            return $this->createResourceWithKey($key, $data);
        } catch (BaseExcption $e) {
            $status = $e->getCode();
        } catch (Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->craeteErrorResponseWithStatusCode($status);
    }

    private function createResourceWithKey($key, Array $data)
    {
        $resource = $this->hub[$key];
        $result = $resource->put($data);
        $value = $resource->getOne($result);
        return $this->responseFactory->createResponse($value, StatusCode::CREATED);
    }

    public function delete($key, $id)
    {
        try {
            return $this->deleteResourceWithKeyAndId($key, $id);
        } catch (BaseExcption $e) {
            $status = $e->getCode();
        } catch (Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function deleteResourceWithKeyAndId($key, $id)
    {
        $resource = $this->hub[$key];
        $resource->delete($id);
        return $this->createOKResponse($resource);
    }

    private function createErrorResponse($status)
    {
        return $this->responseFactory->createResponse(null, $status);
    }

    private function createOKResponse($result)
    {
        return $this->responseFactory->createResponse($result, StatusCode::OK);
    }
}