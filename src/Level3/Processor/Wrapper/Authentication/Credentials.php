<?php

namespace Level3\Processor\Wrapper\Authentication;

use Level3\Processor\Wrapper\Authorization\Role;

abstract class Credentials
{
    abstract public function isAuthenticated();

    public function __toString()
    {
        $authenticated = $this->isAuthenticated() ? 'true' : 'false';
        return sprintf('Authenticated: %s', $authenticated);
    }
}
