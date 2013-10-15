<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class NotFound extends HTTPException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::NOT_FOUND);
    }
}
