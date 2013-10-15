<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests;

use Level3\FormatterFactory;
use Level3\Formatter\JsonFormatter;
use Level3\Formatter\XmlFormatter;

class FormatterFactoryTest extends TestCase
{
    public function testCreateWithOutContentTypes()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\JsonFormatter',
            $factory->create()
        );
    }

    /**
     * @expectedException Level3\Exceptions\NotAcceptable
     */
    public function testCreateWithInvalidContentTypes()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\JsonFormatter',
            $factory->create(array('foo'))
        );
    }

    public function testCreateWithInvalidContentTypesWithAvoidNotAcceptable()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\JsonFormatter',
            $factory->create(array('foo'), true)
        );
    }

    public function testCreateWithJsonFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\JsonFormatter',
            $factory->create(array(JsonFormatter::CONTENT_TYPE), true)
        );
    }

    public function testCreateWithXmlFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\XmlFormatter',
            $factory->create(array(XmlFormatter::CONTENT_TYPE), true)
        );
    }
}
