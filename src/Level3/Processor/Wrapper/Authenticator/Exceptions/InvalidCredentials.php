<?php

namespace Level3\Processor\Wrapper\Authenticator\Exceptions;

use Level3\Exceptions\HTTPException;
use Teapot\StatusCode;

class InvalidCredentials extends HTTPException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::UNAUTHORIZED);
    }
}