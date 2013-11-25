<?php

namespace Level3\Tests\Resource\Format\Reader\HAL;

use Level3\Tests\Resource\Format\Reader\ReaderTest;

class XMLReaderTest extends ReaderTest
{
    protected $mime = 'application/hal+xml';
    protected $reader = 'Level3\Resource\Format\Reader\HAL\XMLReader';
    protected $writer = 'Level3\Resource\Format\Writer\HAL\XMLWriter';
    protected $from = 'Reader/HAL/XMLReader.xml';
}
