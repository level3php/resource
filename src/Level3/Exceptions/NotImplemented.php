<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class NotImplemented extends HTTPException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::NOT_IMPLEMENTED);
    }
}
