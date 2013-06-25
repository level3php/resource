<?php

namespace Level3\Security\Authentication\Exceptions;

use Level3\Security\Authentication\User;

class UserNotFound extends \RuntimeException
{
    public function __construct($apiKey)
    {
        parent::__construct(sprintf('User with API Key "%s" not found', $apiKey));
    }
}