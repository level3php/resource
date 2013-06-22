<?php

namespace Level3\ResourceRepository\Exception;

use Teapot\StatusCode;

class Conflict extends BaseException{
    public function __construct()
    {
        parent::__construct('', StatusCode::CONFLICT);
    }
}