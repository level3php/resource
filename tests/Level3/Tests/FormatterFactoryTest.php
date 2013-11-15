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

use Level3\Resource\FormatterFactory;
use Level3\Resource\Formatter\HAL\JsonFormatter;
use Level3\Resource\Formatter\HAL\XMLFormatter;

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

    /**
     * @expectedException Level3\Exceptions\NotAcceptable
     */
    public function testCreateWithInvalidContentTypes()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\HAL\JsonFormatter',
            $factory->create(['foo'])
        );
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
            $factory->create([JsonFormatter::CONTENT_TYPE], true)
        );
    }

    public function testCreateWithXMLFormatter()
    {
        $factory = new FormatterFactory();
        $this->assertInstanceOf(
            'Level3\Resource\Formatter\HAL\XMLFormatter',
            $factory->create([XMLFormatter::CONTENT_TYPE], true)
        );
    }
}
