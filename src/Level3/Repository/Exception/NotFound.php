<?php

namespace Level3\Repository\Exception;

use Teapot\StatusCode;

class NotFound extends BaseException
{
    public function __construct()
    {
        parent::__construct('', StatusCode::NOT_FOUND);
    }
}