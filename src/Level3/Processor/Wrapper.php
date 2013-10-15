<?php

namespace Level3\Processor;

use Level3\Level3;
use Level3\Messages\Request;
use Closure;

abstract class Wrapper
{
    protected $level3;

    public function setLevel3(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function getLevel3()
    {
        return $this->level3;
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

    public function options(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }

    public function error(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request, __FUNCTION__);
    }

    abstract protected function processRequest(Closure $execution, Request $request, $method);
}
