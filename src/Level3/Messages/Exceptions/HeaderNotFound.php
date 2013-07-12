<?php

namespace Level3\Messages\Exceptions;

class HeaderNotFound extends MessageParsingError
{
    public function __construct($headerName)
    {
        parent::__construct(sprintf('Header "%s" not found', $headerName));
    }
}