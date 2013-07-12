<?php

namespace Level3\Security\Authentication;
use Level3\Messages\Request;

interface AuthenticationMethod
{
    public function authenticateRequest(Request $request);
}
