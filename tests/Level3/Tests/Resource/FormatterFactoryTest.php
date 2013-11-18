<?php

namespace Level3\Tests;

use Level3\Resource\FormatterFactory;
use Level3\Resource\Formatter\HAL;
use Level3\Resource\Formatter\Siren;

class FormatterFactoryTest extends TestCase
{
    public function testCreateWithOutContentTypes()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\HAL\JsonFormatter',
            $factory->create()
        );
    }

    public function testCreateWithInvalidContentTypes()
    {
        $factory = new FormatterFactory();
        $this->assertNull($factory->create(['foo']));
    }

    public function testCreateWithInvalidContentTypesWithAvoidNotAcceptable()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\HAL\JsonFormatter',
            $factory->create(['foo'], true)
        );
    }

    public function testCreateWithJsonFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\HAL\JsonFormatter',
            $factory->create([HAL\JsonFormatter::CONTENT_TYPE], true)
        );
    }

    public function testCreateWithXMLFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\HAL\XMLFormatter',
            $factory->create([HAL\XMLFormatter::CONTENT_TYPE], true)
        );
    }

    public function testCreateWithSirenJsonFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\Siren\JsonFormatter',
            $factory->create([Siren\JsonFormatter::CONTENT_TYPE], true)
        );
    }
}
