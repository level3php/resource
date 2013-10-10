<?php

namespace Level3\Processor\Wrapper\Authentication;
use Level3\Messages\Request;

interface AuthenticationMethod
{
    public function authenticateRequest(Request $request);
}
