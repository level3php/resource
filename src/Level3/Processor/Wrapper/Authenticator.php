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

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $this->authenticateRequest($request);
        $response = $execution($request);
    }

    protected function authenticateRequest(Request $request)
    {
        $this->method->authenticateRequest($request);
    }
}