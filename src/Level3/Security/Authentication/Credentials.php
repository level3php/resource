<?php

namespace Level3\Security\Authentication;

use Level3\Security\Authorization\Role;

abstract class Credentials
{
    abstract public function isAuthenticated();

    public function __toString()
    {
        $authenticated = $this->isAuthenticated() ? 'true' : 'false';
        return sprintf('Authenticated: %s', $authenticated);
    }
}
