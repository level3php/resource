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
use Level3\Formatter\HALJsonFormatter;
use Level3\Formatter\HALXmlFormatter;

class FormatterFactoryTest extends TestCase
{
    public function testCreateWithOutContentTypes()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\HALJsonFormatter',
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
            'Level3\Formatter\HALJsonFormatter',
            $factory->create(['foo'])
        );
    }

    public function testCreateWithInvalidContentTypesWithAvoidNotAcceptable()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\HALJsonFormatter',
            $factory->create(['foo'], true)
        );
    }

    public function testCreateWithJsonFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\HALJsonFormatter',
            $factory->create([HALJsonFormatter::CONTENT_TYPE], true)
        );
    }

    public function testCreateWithXmlFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Formatter\HALXmlFormatter',
            $factory->create([HALXmlFormatter::CONTENT_TYPE], true)
        );
    }
}
