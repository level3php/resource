<?php

namespace Level3\ResourceRepository\Exception;

use Teapot\StatusCode;

class NoContent extends BaseException
{
    public function __construct()
    {
        parent::__construct('', StatusCode::NO_CONTENT);
    }
}