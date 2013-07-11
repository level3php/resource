<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class NotAcceptable extends BaseException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::NOT_ACCEPTABLE);
    }
}