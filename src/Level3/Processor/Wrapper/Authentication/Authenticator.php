<?php

namespace Level3\Processor\Wrapper\Authentication;

use Level3\Processor\Wrapper;
use Level3\Messages\Request;
use Closure;

class Authenticator extends Wrapper
{
    private $authenticationMethod;

    public function setAuthenticationMethod(AuthenticationMethod $method)
    {
        $this->authenticationMethod = $method;
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $this->authenticateRequest($request);
        return $execution($request);
    }

    private function authenticateRequest(Request $request)
    {
        $this->authenticationMethod->authenticateRequest($request);
    }
}
