<?php

namespace Level3\Tests\Resource\Format\Writer\HAL;

use Level3\Tests\Resource\Format\Writer\WriterTest;

class XMLWriterTest extends WriterTest
{
    protected $mime = 'application/hal+xml';
    protected $class = 'Level3\Resource\Format\Writer\HAL\XMLWriter';
    protected $toNonPretty = 'Writer/HAL/XMLWriterNonPretty.xml';
    protected $toPretty = 'Writer/HAL/XMLWriterPretty.xml';
}
