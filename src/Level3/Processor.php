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
        $self = $this;

        return $this->execute('find', $request, function(Request $request) use ($self) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $filters = $request->getFilters();
            
            $repository = $self->getRepository($key);
            $resource = $repository->find($attributes, $filters);

            return $self->createResponse($request, $resource);
        });
    }

    public function get(Request $request)
    {
        $self = $this;

        return $this->execute('get', $request, function(Request $request) use ($self) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();

            $repository = $self->getRepository($key);
            $resource = $repository->get($attributes);

            return $self->createResponse($request, $resource);
        });
    }

    public function post(Request $request)
    {
        $self = $this;

        return $this->execute('post', $request, function(Request $request) use ($self) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $content = $request->getContent();

            $repository = $self->getRepository($key);
            $resource = $repository->post($attributes, $content);

            $response = $self->createResponse($request, $resource);
            $response->setStatusCode(StatusCode::CREATED);

            return $response;
        });
    }

    public function patch(Request $request)
    {
        $self = $this;

        return $this->execute('patch', $request, function(Request $request) use ($self) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $content = $request->getContent();

            $repository = $self->getRepository($key);
            $resource = $repository->patch($attributes, $content);

            return $self->createResponse($request, $resource);
        });
    }

    public function put(Request $request)
    {
        $self = $this;

        return $this->execute('put', $request, function(Request $request) use ($self) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();
            $content = $request->getContent();

            $repository = $self->getRepository($key);
            $resource = $repository->put($attributes, $content);

            return $self->createResponse($request, $resource);
        });
    }

    public function delete(Request $request)
    {
        $self = $this;

        return $this->execute('delete', $request, function(Request $request) use ($self) { 
            $key = $request->getKey();
            $attributes = $request->getAttributes();

            $repository = $self->getRepository($key);
            $resource = $repository->delete($attributes);

            return $self->createResponse($request);
        });
    }

    protected function execute($method, Request $request, Closure $execution)
    {
        $processors = $this->level3->getProcessorWrappers();
        foreach ($processors as $processor) {
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
