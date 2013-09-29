<?php

namespace Level3;

use Level3\Exceptions\HTTPException;
use Level3\Exceptions\NotFound;
use Level3\Messages\Response;
use Level3\Messages\Request;

use Teapot\StatusCode;
use RuntimeException;
use Exception;
use Closure;

class Processor
{
    private $level3;
    private $lastException;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function find(Request $request)
    {
        return $this->execute('find', $request, function(Request $request) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $filters = $request->getFilters();
            
            $repository = $this->getRepository($key);
            $resource = $repository->find($attributes, $filters);

            return $this->createResponse($request, $resource);
        });
    }

    public function get(Request $request)
    {
        return $this->execute('get', $request, function(Request $request) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();

            $repository = $this->getRepository($key);
            $resource = $repository->get($attributes);

            return $this->createResponse($request, $resource);
        });
    }

    public function post(Request $request)
    {
        return $this->execute('post', $request, function(Request $request) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $content = $request->getContent();

            $repository = $this->getRepository($key);
            $resource = $repository->post($attributes, $content);

            $response = $this->createResponse($request, $resource);
            $response->setStatusCode(StatusCode::CREATED);

            return $response;
        });
    }

    public function patch(Request $request)
    {
        return $this->execute('patch', $request, function(Request $request) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $content = $request->getContent();

            $repository = $this->getRepository($key);
            $resource = $repository->patch($attributes, $content);

            return $this->createResponse($request, $resource);
        });
    }

    public function put(Request $request)
    {
        return $this->execute('put', $request, function(Request $request) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $content = $request->getContent();

            $repository = $this->getRepository($key);
            $resource = $repository->put($attributes, $content);

            return $this->createResponse($request, $resource);
        });
    }

    public function delete(Request $request)
    {
        return $this->execute('delete', $request, function(Request $request) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();

            $repository = $this->getRepository($key);
            $resource = $repository->delete($attributes);

            return $this->createResponse($request);
        });
    }

    protected function execute($method, Request $request, Closure $execution)
    {
        $processors = $this->level3->getProcessorWrappers();
        foreach (array_reverse($processors) as $processor) {
            $execution = function($request) use ($execution, $method, $processor) {
                return $processor->$method($execution, $request);
            };
        }
    
        return $execution($request);
    }

    protected function createResponse(Request $request, Resource $resource = null)
    {
        $response = new Response();
        if ($resource) {
            $response->setStatusCode(StatusCode::OK);
            $response->setResource($resource);
            $response->setFormatter($request->getFormatter());
        } else {
            $response->setStatusCode(StatusCode::NO_CONTENT);
        }

        return $response;
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
