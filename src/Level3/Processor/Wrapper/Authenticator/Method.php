<?php

namespace Level3\Processor\Wrapper\Authenticator;
use Level3\Messages\Request;
use Level3\Messages\Response;

interface Method
{
    public function authenticate(Request $request);
    public function modifyResponse(Response $response);
}
