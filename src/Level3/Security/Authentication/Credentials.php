<?php

namespace Level3\Security\Authentication;

use Level3\Security\Authorization\Role;

class Credentials
{
    const ANONYMOUS_CREDENTIALS_NAME = 'Anonymous Credentials';
    const ANONYMOUS_API_KEY = 'anonymous';

    private $role;

    public function __construct()
    {
        $this->role = new Role();
    }

    public function isAuthenticated()
    {
        return false;
    }

    public function getFullName()
    {
        return self::ANONYMOUS_CREDENTIALS_NAME;
    }

    public function getApiKey()
    {
        return self::ANONYMOUS_API_KEY;
    }

    public function getRole()
    {
        return $this->role;
    }
}