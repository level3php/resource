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

class AResourceTest extends TestCase {
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

    public function testResourceAsJson()
    {
        $resource = new Resource();
        $this->assertEquals('[]', $resource->asJson());
    }

    public function testResourceAsXML()
    {
        $resource = new Resource();
        $this->assertEquals("<?xml version=\"1.0\"?>\n<resource/>\n", $resource->asXml());
    }
}