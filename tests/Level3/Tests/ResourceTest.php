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
use Level3\Resource;
use DateTime;

class ResourceTest extends TestCase
{
    public function setUp()
    {
        $this->resource = new Resource();
    }

    public function testSetId()
    {
        $id = 'foo';

        $this->assertSame($this->resource, $this->resource->setId($id));
        $this->assertSame($id, $this->resource->getId());
    }

    public function testAddLink()
    {
        $link = $this->createLinkMock();
        $this->resource->addLink('foo', $link);

        $links = $this->resource->getLinks();
        $this->assertSame($link, $links['foo'][0]);
    }

    public function testGetLinkResource()
    {
        $link = $this->createLinkMock();
        $this->resource->addLink('foo', $link);

        $links = $this->resource->getLinksByRel('foo');
        $this->assertSame($link, $links[0]);

        $this->assertNull($this->resource->getLinksByRel('bar'));
    }

    public function testLinkResource()
    {
        $linkedResource = new Resource($this->repository);
        $linkedResource->setURI('foo');

        $this->resource->linkResource('foo', $linkedResource);
        $links = $this->resource->getLinks();
        $this->assertInstanceOf('Level3\Resource\Link', $links['foo'][0]);
        $this->assertSame('foo', $links['foo'][0]->getHref());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLinkResourceInvalid()
    {
        $linkedResource = new Resource($this->repository);

        $this->resource->linkResource('foo', $linkedResource);
        $links = $this->resource->getLinks();
        $this->assertInstanceOf('Level3\Resource\Link', $links['foo'][0]);
        $this->assertSame('foo', $links['foo'][0]->getHref());
    }

    public function testAddResource()
    {
        $resource = new Resource($this->repository);

        $this->resource->addResource('foo', $resource);
        $resources = $this->resource->getResources();
        $this->assertSame($resource, $resources['foo'][0]);
    }

    public function testGetResourceByRel()
    {
        $resource = new Resource($this->repository);

        $this->resource->addResource('foo', $resource);
        $resources = $this->resource->getResourcesByRel('foo');
        $this->assertSame($resource, $resources[0]);

        $this->assertNull($this->resource->getResourcesByRel('bar'));
    }

    public function testSetData()
    {
        $this->assertSame($this->resource, $this->resource->setData(array('foo' => 'bar')));
        $this->assertSame(array('foo' => 'bar'), $this->resource->getData());
    }

    public function testAddData()
    {
        $this->assertSame($this->resource, $this->resource->addData('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->resource->getData());
    }

    public function testSetURI()
    {
        $uri = 'foo';

        $this->assertSame($this->resource, $this->resource->setURI($uri));
        $this->assertSame($uri, $this->resource->getURI());
    }

    public function testSetLastUpdate()
    {
        $date = new DateTime();

        $this->assertSame($this->resource, $this->resource->setLastUpdate($date));
        $this->assertSame($date, $this->resource->getLastUpdate());
    }

    public function testSetCache()
    {
        $cache = 10;

        $this->assertSame($this->resource, $this->resource->setCache($cache));
        $this->assertSame($cache, $this->resource->getCache());
    }
}
