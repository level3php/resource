<?php

namespace Level3\Processor\Wrapper\Authenticator;

use Level3\Processor\Wrapper\Authorization\Role;

class Credentials
{
    protected $isAuthenticated;

    public function __construct($isAuthenticated)
    {
        $this->isAuthenticated = $isAuthenticated;
    }

    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    public function __toString()
    {
        $authenticated = $this->isAuthenticated() ? 'true' : 'false';
        
        return sprintf('Authenticated: %s', $authenticated);
    }
}
