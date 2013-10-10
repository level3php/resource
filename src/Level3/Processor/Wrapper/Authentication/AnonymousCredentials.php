<?php

namespace Level3\Processor\Wrapper\Authentication;

class AnonymousCredentials extends Credentials
{
    public function isAuthenticated()
    {
        return false;
    }
}
