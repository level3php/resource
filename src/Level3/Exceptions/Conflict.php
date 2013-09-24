<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class Conflict extends HTTPException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::CONFLICT);
    }
}