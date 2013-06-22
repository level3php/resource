<?php

namespace Level3\ResourceRepository\Exception;

use Teapot\StatusCode;

class NotFound extends BaseException
{
    public function __construct()
    {
        parent::__construct('', StatusCode::NOT_FOUND);
    }
}