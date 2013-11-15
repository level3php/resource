<?php

namespace Level3;

use Level3\Resource\Resource;
use Level3\Exceptions\NotFound;
use Level3\Exceptions\NotImplemented;
use Level3\Messages\Response;
use Level3\Messages\Request;

use Teapot\StatusCode;
use RuntimeException;
use Exception;
use Closure;

class Processor
{
    private $level3;

    public function setLevel3(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function find(Request $request)
    {
        return $this->execute('find', $request, function (Request $request) {
            $key = $request->getKey();
            $repository = $this->getRepository($key);

            $attributes = $request->getAttributes();
            $filters = $request->getFilters();
            $resource = $repository->find($attributes, $filters);

            $this->applyExpandToResource($request->getExpand(), $resource);

            return Response::createFromResource($request, $resource);
        });
    }

    public function get(Request $request)
    {
        return $this->execute('get', $request, function (Request $request) {
            $key = $request->getKey();
            $repository = $this->getRepository($key);

            $attributes = $request->getAttributes();
            $resource = $repository->get($attributes);

            $this->applyExpandToResource($request->getExpand(), $resource);

            return Response::createFromResource($request, $resource);
        });
    }

    protected function applyExpandToResource(Array $paths, Resource $resource)
    {
        foreach ($paths as $path) {
            $resource->expandLinkedResourcesTree($path);
        }
    }

    public function post(Request $request)
    {
        return $this->execute('post', $request, function (Request $request) {
            $key = $request->getKey();
            $repository = $this->getRepository($key);

            $attributes = $request->getAttributes();
            $content = $request->getContent();
            $resource = $repository->post($attributes, $content);

            $response = Response::createFromResource($request, $resource);
            $response->setStatusCode(StatusCode::CREATED);

            return $response;
        });
    }

    public function patch(Request $request)
    {
        return $this->execute('patch', $request, function (Request $request) {
            $key = $request->getKey();
            $repository = $this->getRepository($key);

            $attributes = $request->getAttributes();
            $content = $request->getContent();
            $resource = $repository->patch($attributes, $content);

            return Response::createFromResource($request, $resource);
        });
    }

    public function put(Request $request)
    {
        return $this->execute('put', $request, function (Request $request) {
            $key = $request->getKey();
            $repository = $this->getRepository($key);

            $attributes = $request->getAttributes();
            $content = $request->getContent();
            $resource = $repository->put($attributes, $content);

            return Response::createFromResource($request, $resource);
        });
    }

    public function delete(Request $request)
    {
        return $this->execute('delete', $request, function (Request $request) {
            $key = $request->getKey();
            $repository = $this->getRepository($key);

            $attributes = $request->getAttributes();
            $repository->delete($attributes);

            return new Response(null, StatusCode::NO_CONTENT);
        });
    }

    public function options(Request $request)
    {
        return $this->execute('options', $request, function () {
            throw new NotImplemented();
        });
    }

    public function error(Request $request, Exception $exception)
    {
        return $this->execute('error', $request, function (Request $request) use ($exception) {
            return Response::createFromException($request, $exception);
        });
    }

    protected function execute($method, Request $request, Closure $execution)
    {
        $wrappers = $this->level3->getProcessorWrappers();
        foreach ($wrappers as $wrapper) {
            $execution = function ($request) use ($execution, $method, $wrapper) {
                return $wrapper->$method($execution, $request);
            };
        }

        return $execution($request);
    }

    protected function getRepository($key)
    {
        try {
            return $this->level3->getRepository($key);
        } catch (RuntimeException $e) {
            throw new NotFound();
        }
    }
}
