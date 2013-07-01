<?php

namespace Level3\Security\Authentication;
use Level3\Messages\Request;

interface Method
{
    public function authenticateRequest(Request $request);
}
