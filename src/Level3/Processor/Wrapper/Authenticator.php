<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Messages\Response;

use Level3\Processor\Wrapper;
use Level3\Processor\Wrapper\Authenticator\Method;

use Closure;

class Authenticator extends Wrapper
{
    const PRIORITY_LOW = 10;
    const PRIORITY_NORMAL = 20;
    const PRIORITY_HIGH = 30;

    protected $methods = array();

    public function clearMethods()
    {
        $this->methods = array();
    }

    public function addMethod(Method $method, $priority = self::PRIORITY_NORMAL)
    {
        $this->methods[$priority][] = $method;
    }

    public function getMethods()
    {
        $result = array();

        ksort($this->methods);
        foreach ($this->methods as $priority => $methods) {
            $result = array_merge($result, $methods);
        }

        return $result;
    }

    public function error(Closure $execution, Request $request)
    {
        return $execution($request);
    }

    protected function processRequest(Closure $execution, Request $request, $httpMethod)
    {
        $this->authenticate($request);
        $response = $execution($request);
        $this->modifyResponse($response);

        return $response;
    }

    protected function authenticate(Request $request)
    {
        foreach ($this->getMethods() as $method) {
            $this->authenticateWithMethod($method, $request);
        }
    }

    protected function authenticateWithMethod(Method $method, Request $request)
    {
        $method->authenticate($request);
    }

    protected function modifyResponse(Response $response)
    {
        foreach ($this->getMethods() as $method) {
            $this->modifyResponseWithMethod($method, $response);
        }
    }

    protected function modifyResponseWithMethod(Method $method, Response $response)
    {
        $method->modifyResponse($response);
    }

}
