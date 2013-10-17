<?php

namespace Level3\Processor\Wrapper\Authenticator;
use Level3\Messages\Request;
use Level3\Messages\Response;

interface Method
{
    public function authenticateRequest(Request $request, $httpMethod);
    public function modifyResponse(Response $response, $httpMethod);
}
