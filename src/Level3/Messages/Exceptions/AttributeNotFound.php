<?php

namespace Level3\Messages\Exceptions;

class AttributeNotFound extends MessageParsingError
{
    public function __construct($key)
    {
        parent::__construct(printf('Attribute with key "%s" not found', $key));
    }
}