<?php

namespace Level3;

use Level3\Exceptions\HTTPException;
use Level3\Exceptions\NotFound;
use Level3\Messages\Response;
use Level3\Messages\Request;

use Teapot\StatusCode;
use RuntimeException;
use Exception;

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
        $attributes = $request->getAttributes();
        $filters = $request->getFilters();
        
        return $this->callRepositoryMethod($request, 'find', $attributes, $filters);
    }

    public function get(Request $request)
    {
        $attributes = $request->getAttributes();

        return $this->callRepositoryMethod($request, 'get', $attributes);
    }

    public function post(Request $request)
    {
        $attributes = $request->getAttributes();
        $content = $request->getContent();

        $response = $this->callRepositoryMethod($request, 'post', $attributes, $content);
        $response->setStatusCode(StatusCode::CREATED);

        return $response;
    }

    public function patch(Request $request)
    {
        $attributes = $request->getAttributes();
        $content = $request->getContent();

        return $this->callRepositoryMethod($request, 'patch', $attributes, $content);
    }

    public function put(Request $request)
    {
        $attributes = $request->getAttributes();
        $content = $request->getContent();

        return $this->callRepositoryMethod($request, 'put', $attributes, $content);
    }

    public function delete(Request $request)
    {
        $attributes = $request->getAttributes();

        return $this->callRepositoryMethod($request, 'delete', $attributes);
    }

    protected function callRepositoryMethod($request, $method)
    {
        $args = func_get_args();
        $key = $request->getKey();

        try {
            $repository = $this->getRepository($key);
            if (count($args) == 3 ) $resource = $repository->$method($args[2]);
            if (count($args) == 4 ) $resource = $repository->$method($args[2], $args[3]);

            if (!$resource) {
                return $this->createEmptyResponse();
            }

            return $this->createResourceResponse($request, $resource);
        } catch (Exception $exception) {
            return $this->createExceptionResponse($request, $exception);
        }

        return $response;
    }

    protected function createEmptyResponse()
    {
        $response = new Response();
        $response->setStatusCode(StatusCode::NO_CONTENT);

        return $response;
    }

    protected function createResourceResponse(Request $request, Resource $resource)
    {
        $response = new Response();
        $response->setStatusCode(StatusCode::OK);
        $response->setResource($resource);
        $response->setFormatter($request->getFormatter());

        return $response;
    }

    protected function createExceptionResponse(Request $request, Exception $exception)
    {
        $code = StatusCode::INTERNAL_SERVER_ERROR;
        if ($exception instanceOf HTTPException) {
            $code = $exception->getCode();
        }

        $response = new Response();
        $response->setStatusCode($code);
        $response->setFormatter($request->getFormatter());
        //$response->setResource($resource);

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
