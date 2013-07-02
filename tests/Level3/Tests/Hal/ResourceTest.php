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
use Level3\Hal\Resource;
use Level3\Hal\Link;
use Mockery as m;

class ResourceTest extends TestCase {
    const IRRELEVANT_FORMATTED_RESOURCE = 'X';

    public function testHal()
    {
        $uri = '/test';
        $expected = array('foo' => 'bar');

        $resource = new Resource($uri, $expected);
        $this->assertSame($expected, $resource->getData());
        $this->assertSame($uri, $resource->getURI());
    }

    public function testHalDefault()
    {
        $resource = new Resource();
        $this->assertEquals(array(), $resource->getData());
        $this->assertNull($resource->getURI());
    }

    public function testAddLinkAndGetLinks()
    {
        $resource = new Resource();
        $link = new Link();

        $this->assertSame($resource, $resource->addLink('foo', $link));

        $links = $resource->getLinks();
        $this->assertSame($link, $links['foo'][0]);
    }

    public function testAddResourceAndGetResources()
    {
        $resource = new Resource();
        $sub = new Resource();

        $this->assertSame($resource, $resource->addResource('foo', $sub));

        $resources = $resource->getResources();
        $this->assertSame($sub, $resources['foo'][0]);
    }

    public function testSetAndGetData()
    {
        $resource = new Resource();
        $expected = array('foo');

        $this->assertSame($resource, $resource->setData($expected));
        $this->assertSame($expected, $resource->getData());
    }

    public function testSetAndGetUri()
    {
        $resource = new Resource();
        $expected = 'foo';

        $this->assertSame($resource, $resource->setURI($expected));
        $this->assertSame($expected, $resource->getURI());
    }

    public function testFormat()
    {
        $resource = new Resource();
        $formatterMock = m::mock('Level3\Hal\Formatter\Formatter');
        $formatterMock->shouldReceive('format')->with($resource)->once()->andReturn(self::IRRELEVANT_FORMATTED_RESOURCE);
        $resource->setFormatter($formatterMock);

        $result = $resource->format();

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_FORMATTED_RESOURCE));
    }

    public function testFormatPretty()
    {
        $resource = new Resource();
        $formatterMock = m::mock('Level3\Hal\Formatter\Formatter');
        $formatterMock->shouldReceive('formatPretty')->with($resource)->once()->andReturn(self::IRRELEVANT_FORMATTED_RESOURCE);
        $resource->setFormatter($formatterMock);

        $result = $resource->formatPretty();

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_FORMATTED_RESOURCE));
    }
}