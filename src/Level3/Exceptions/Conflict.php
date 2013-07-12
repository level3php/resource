<?php

namespace Level3\Exceptions;

use Teapot\StatusCode;

class Conflict extends BaseException
{
    public function __construct($message = '')
    {
        parent::__construct($message, StatusCode::CONFLICT);
    }
}