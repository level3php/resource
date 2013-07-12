<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class NoContent extends BaseException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::NO_CONTENT);
    }
}