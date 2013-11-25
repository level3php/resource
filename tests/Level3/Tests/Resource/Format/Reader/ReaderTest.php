<?php

namespace Level3\Tests\Resource\Format\Reader;

use Level3\Tests\TestCase;

abstract class ReaderTest extends TestCase
{
    const EXAMPLE_URI = '/test';

    public function testRead()
    {
        $string = $this->readResource($this->from);

        $reader = new $this->reader(true);
        $resource = $reader->execute($string);
        
        $writer = new $this->writer(true);

        $this->assertSame(
            $string,
            $writer->execute($resource)
        );
    }

    public function testGetContentType()
    {
        $reader = new $this->reader();
        $this->assertSame($this->mime, $reader->getContentType());
    }

    public function readResource($filename)
    {
        return trim(file_get_contents(__DIR__ . '/../../../../Resources/' . $filename));
    }
}
