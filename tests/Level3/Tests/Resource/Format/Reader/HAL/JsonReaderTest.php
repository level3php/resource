<?php

namespace Level3\Tests\Resource\Format\Reader\HAL;

use Level3\Tests\Resource\Format\Reader\ReaderTest;

class JsonReaderTest extends ReaderTest
{
    protected $mime = 'application/hal+json';
    protected $reader = 'Level3\Resource\Format\Reader\HAL\JsonReader';
    protected $writer = 'Level3\Resource\Format\Writer\HAL\JsonWriter';
    protected $from = 'Reader/HAL/JsonReader.json';
}
