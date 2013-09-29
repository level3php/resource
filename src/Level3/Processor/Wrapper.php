<?php

namespace Level3\Processor;

use Level3\Messages\Request;
use Closure;

abstract class Wrapper
{
    private $level3;

    public function setLeve3(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function getLeve3($level3)
    {
        return $this->level3;
    }

    abstract public function find(Closure $execution, Request $request);

    abstract public function get(Closure $execution, Request $request);

    abstract public function post(Closure $execution, Request $request);

    abstract public function put(Closure $execution, Request $request);
    
    abstract public function patch(Closure $execution, Request $request);

    abstract public function delete(Closure $execution, Request $request);
}