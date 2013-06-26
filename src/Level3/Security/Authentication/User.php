<?php

namespace Level3\Security\Authentication;

class User 
{
    const ANONYMOUS_USER_NAME = 'Anonymous User';
    const ANONYMOUS_API_KEY = 'anonymous';

    public function isAuthenticated()
    {
        return false;
    }

    public function getFullName()
    {
        return self::ANONYMOUS_USER_NAME;
    }

    public function getApiKey()
    {
        return self::ANONYMOUS_API_KEY;
    }
}