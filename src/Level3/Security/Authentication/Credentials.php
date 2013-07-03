<?php

namespace Level3\Security\Authentication;

class Credentials
{
    const ANONYMOUS_CREDENTIALS_NAME = 'Anonymous Credentials';
    const ANONYMOUS_API_KEY = 'anonymous';

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
}