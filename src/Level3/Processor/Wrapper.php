<?php

namespace Level3\Processor;

use Level3\Level3;
use Level3\Messages\Request;
use Closure;

interface Wrapper
{
    public function find(Closure $execution, Request $request);

    public function get(Closure $execution, Request $request);

    public function post(Closure $execution, Request $request);

    public function put(Closure $execution, Request $request);
    
    public function patch(Closure $execution, Request $request);

    public function delete(Closure $execution, Request $request);
}