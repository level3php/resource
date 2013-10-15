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
        $this->setAllowCredentialsIfNeeded();

        $response = $execution($request);
        $this->modifyResponse($response, 'error');

        return $response;
    }

    protected function processRequest(Closure $execution, Request $request, $httpMethod)
    {
        $this->setAllowCredentialsIfNeeded();

        $this->authenticateRequest($request, $httpMethod);
        $response = $execution($request);
        $this->modifyResponse($response, $httpMethod);

        return $response;
    }

    protected function authenticateRequest(Request $request, $httpMethod)
    {
        foreach ($this->getMethods() as $method) {
            $this->authenticateWithMethod($method, $request, $httpMethod);
        }
    }

    protected function authenticateWithMethod(Method $method, Request $request, $httpMethod)
    {
        $method->authenticateRequest($request, $httpMethod);
    }

    protected function modifyResponse(Response $response, $httpMethod)
    {
        foreach ($this->getMethods() as $method) {
            $this->modifyResponseWithMethod($method, $response, $httpMethod);
        }
    }

    protected function modifyResponseWithMethod(Method $method, Response $response, $httpMethod)
    {
        $method->modifyResponse($response, $httpMethod);
    }

    protected function setAllowCredentialsIfNeeded()
    {
        if (!$level3 = $this->getLevel3()) {
            return false;
        }

        $corsClass = 'Level3\Processor\Wrapper\CrossOriginResourceSharing';
        $cors = $level3->getProcessorWrappersByClass($corsClass);

        if ($cors) {
            $cors->setAllowCredentials(true);
        }
    }

}
