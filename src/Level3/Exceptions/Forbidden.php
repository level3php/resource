<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class Forbidden extends BaseException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::FORBIDDEN);
    }
}