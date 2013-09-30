<?php

namespace Level3\Processor\Wrapper;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Level3\Messages\Request;
use Level3\Messages\Response;
use Level3\Processor\Wrapper;
use Closure;

class RequestLogger implements Wrapper
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function find(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }
    
    public function get(Closure $execution, Request $request)
    {
        return $this->processRequest($execution ,$request, __FUNCTION__);
    }

    public function post(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }

    public function put(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }

    public function patch(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }

    public function delete(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $response = $execution($request);
        
        $level = $this->getLogLevel($request, $response, $method);
        $log = $this->getLogMessage($request, $response, $method);
        
        $this->logger->$level($log);

        return $response;
    }

    protected function getLogLevel(Request $request, Response $response, $method)
    {
        $code = $response->getStatusCode();

        if ($code >= 200 && $code < 400) {
            return LogLevel::INFO;
        } else if ($code >= 400 && $code < 500) {
            return LogLevel::WARNING;
        } else {
            return LogLevel::ERROR;
        }
    }

    protected function getLogMessage(Request $request, Response $response, $method)
    {
        $key = $request->getKey();
        $attributes = $request->getAttributes();

        return sprintf('%s::%s - %s', $key, $method, null);
    }
}