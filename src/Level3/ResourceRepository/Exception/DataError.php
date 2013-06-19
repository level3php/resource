<?php

namespace Level3\ResourceRepository\Exception;

use Teapot\StatusCode;

class DataError extends BaseException
{
    public function __construct()
    {
        parent::__construct('', StatusCode::BAD_REQUEST);
    }
}