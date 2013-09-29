<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Processor\Wrapper;
use Level3\Exceptions\HTTPException;
use Exception;
use Teapot\StatusCode;

class ExceptionHandler implements Wrapper
{
    public function find(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }
    
    public function get(Closure $execution, Request $request)
    {
        return $this->processRequest($execution ,$request);
    }

    public function post(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    public function put(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    public function delete(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    protected function processRequest(Closure $execution, Request $request)
    {
        try {
            return $execution($request);
        } catch (Exception $exception) {
            return $this->createExceptionResponse($request, $exception);
        }
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

        return $response;
    }
}