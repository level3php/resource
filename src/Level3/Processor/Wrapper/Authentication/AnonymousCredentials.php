<?php

namespace Level3\Security\Authentication;

class AnonymousCredentials extends Credentials
{
    public function isAuthenticated()
    {
        return false;
    }
}
