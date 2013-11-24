<?php

namespace Level3\Tests\Resource\Format\Writer\HAL;

use Level3\Tests\Resource\Format\Writer\WriterTest;

class JsonWriterTest extends WriterTest
{
    protected $class = 'Level3\Resource\Format\Writer\HAL\JsonWriter';
    protected $toNonPretty = 'ToFormatHALJSONNonPretty.json';
    protected $toPretty = 'ToFormatHALJSONPretty.json';
    protected $from = 'FromFormatHALJSON.json';
}
