<?php

namespace Level3\Repository\Exception;

use Teapot\StatusCode;

class NoContent extends BaseException
{
    public function __construct()
    {
        parent::__construct('', StatusCode::NO_CONTENT);
    }
}