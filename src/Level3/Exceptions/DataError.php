<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class DataError extends BaseException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::BAD_REQUEST);
    }
}