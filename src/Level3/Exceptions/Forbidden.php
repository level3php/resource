<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class Forbidden extends HTTPException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::FORBIDDEN);
    }
}