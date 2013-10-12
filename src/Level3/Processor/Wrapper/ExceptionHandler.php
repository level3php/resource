<?php

namespace Level3\Processor\Wrapper;

use Level3\Resource;
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
            if ($method != 'error') {
                return $this->getLevel3()->getProcessor()->error($request, $exception);
            }

            throw $exception;
        }
    }
}