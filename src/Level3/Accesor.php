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

use Level3\Repository\Exception\BaseException;
use Level3\Hal\Resource;
use Teapot\StatusCode;
use Level3\Messages\ResponseFactory;
use Exception;

class Accesor
{
    private $repositoryHub;
    private $responseFactory;

    public function __construct(RepositoryHub $repositoryHub, ResponseFactory $responseFactory)
    {
        $this->repositoryHub = $repositoryHub;
        $this->responseFactory = $responseFactory;
    }

    public function find($key)
    {
        try {
            return $this->findResources($key);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
            var_dump($e);
        }

        return $this->createErrorResponse($status);
    }

    private function findResources($key)
    {
        $repository = $this->repositoryHub->get($key);
        $resource = $repository->find();

        return $this->createOKResponse($resource);
    }

    public function get($key, $id)
    {
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
        $repository = $this->repositoryHub->get($key);
        $resource = $repository->get($id);

        return $this->createOKResponse($resource);
    }

    public function post($key, $id, Array $receivedResourceData)
    {
        try {
            return $this->modifyResource($key, $id, $receivedResourceData);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function modifyResource($key, $id, Array $receivedResourceData)
    {
        $repository = $this->repositoryHub->get($key);
        $repository->post($id, $receivedResourceData);
        $resource = $repository->get($id);

        return $this->createOKResponse($resource);
    }

    public function put($key, Array $receivedResourceData)
    {
        try {
            return $this->createResource($key, $receivedResourceData);
        } catch (BaseException $e) {
            $status = $e->getCode();
        } catch (\Exception $e) {
            $status = StatusCode::INTERNAL_SERVER_ERROR;
        }

        return $this->createErrorResponse($status);
    }

    private function createResource($key, Array $receivedResourceData)
    {
        $repository = $this->repositoryHub->get($key);
        $resource = $repository->put($data);

        return $this->createCreatedResponse($resource);
    }

    public function delete($key, $id)
    {
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
        $repository = $this->repositoryHub->get($key);
        $repository->delete($id);

        return $this->createOKResponse(null);
    }

    private function createErrorResponse($status)
    {
        return $this->responseFactory->createResponse(null, $status);
    }

    private function createOKResponse(Resource $resource)
    {
        return $this->responseFactory->createResponse($resource, StatusCode::OK);
    }

    private function createCreatedResponse(Resource $resource)
    {
        return $this->responseFactory->createResponse($resource, StatusCode::CREATED);
    }
}