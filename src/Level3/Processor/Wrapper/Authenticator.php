<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Processor\Wrapper;
use Level3\Processor\Wrapper\Authenticator\Method;

use Closure;

class Authenticator extends Wrapper
{
    protected $method;

    public function __construct(Method $method)
    {
        $this->method = $method;
    }

    public function error(Closure $execution, Request $request)
    {
        return $execution($request);
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $this->method->authenticate($request);

        $response = $execution($request);
        $this->method->modifyResponse($response);
        return $response;
    }
}