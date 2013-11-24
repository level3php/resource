<?php

namespace Level3\Tests\Resource\Format\Writer\HAL;

use Level3\Tests\Resource\Format\Writer\WriterTest;

class XMLWriterTest extends WriterTest
{
    protected $class = 'Level3\Resource\Format\Writer\HAL\XMLWriter';
    protected $toNonPretty = 'ToFormatHALXMLNonPretty.xml';
    protected $toPretty = 'ToFormatHALXMLPretty.xml';
    protected $from = 'FromFormatHALXML.xml';
}
