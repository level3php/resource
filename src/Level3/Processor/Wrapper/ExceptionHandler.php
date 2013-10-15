<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Processor\Wrapper;

use Exception;
use Closure;

class ExceptionHandler extends Wrapper
{
    public function error(Closure $execution, Request $request)
    {
        return $execution($request);
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        try {
            return $execution($request);
        } catch (Exception $exception) {
            return $this->getLevel3()->getProcessor()->error($request, $exception);
        }
    }
}
