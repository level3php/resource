<?php

namespace Level3\Processor;

use Level3\Level3;
use Level3\Messages\Request;
use Closure;

abstract class Wrapper
{
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

    abstract protected function processRequest(Closure $execution, Request $request, $method);
}