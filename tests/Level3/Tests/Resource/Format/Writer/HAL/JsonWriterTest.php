<?php

namespace Level3\Tests\Resource\Format\Writer\HAL;

use Level3\Tests\Resource\Format\Writer\WriterTest;

class JsonWriterTest extends WriterTest
{
    protected $mime = 'application/hal+json';
    protected $class = 'Level3\Resource\Format\Writer\HAL\JsonWriter';
    protected $toNonPretty = 'Writer/HAL/JsonWriterNonPretty.json';
    protected $toPretty = 'Writer/HAL/JsonWriterPretty.json';
}
