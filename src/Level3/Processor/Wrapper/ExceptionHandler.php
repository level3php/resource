<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Messages\Response;

use Level3\Processor\Wrapper;
use Level3\Exceptions\HTTPException;

use Exception;
use Closure;
use Teapot\StatusCode;

class ExceptionHandler extends Wrapper
{
    protected function processRequest(Closure $execution, Request $request, $method)
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