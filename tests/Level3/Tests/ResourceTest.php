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
use Level3\Resource\Link;
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

    public function testSetLink()
    {
        $link = $this->createLinkMock();
        $this->resource->setLink('foo', $link);

        $links = $this->resource->getLinks();
        $this->assertSame($link, $links['foo']);
    }

    public function testSetLinks()
    {
        $linksExpected = array(
            $this->createLinkMock(),
            $this->createLinkMock()
        );

        $this->resource->setLinks('foo', $linksExpected);

        $links = $this->resource->getLinks();
        $this->assertSame($linksExpected, $links['foo']);
    }

    public function testGetLinkByRel()
    {
        $link = $this->createLinkMock();
        $this->resource->setLink('foo', $link);

        $links = $this->resource->getLinksByRel('foo');
        $this->assertSame($link, $links);

        $this->assertNull($this->resource->getLinksByRel('bar'));
    }

    public function testLinkResource()
    {
        $linkedResource = new Resource($this->repository);
        $linkedResource->setURI('foo');

        $this->resource->linkResource('foo', $linkedResource);
        $links = $this->resource->getLinks();
        $this->assertInstanceOf('Level3\Resource\Link', $links['foo']);
        $this->assertSame('foo', $links['foo']->getHref());
    }

    public function testLinkResources()
    {
        $linkedResourceA = new Resource($this->repository);
        $linkedResourceA->setURI('foo');

        $linkedResourceB = new Resource($this->repository);
        $linkedResourceB->setURI('bar');


        $this->resource->linkResources('foo', array(
            $linkedResourceA,
            $linkedResourceB
        ));

        $links = $this->resource->getLinks();
        $this->assertInstanceOf('Level3\Resource\Link', $links['foo'][0]);
        $this->assertInstanceOf('Level3\Resource\Link', $links['foo'][1]);

        $this->assertSame('foo', $links['foo'][0]->getHref());
        $this->assertSame('bar', $links['foo'][1]->getHref());
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

    public function testToArray()
    {
        $link = new Link('bar');
        $this->resource->setLink('foo', $link);
        $this->resource->setURI('foo');

        $linksExpected = array(
            new Link('bar/foo'),
            new Link('bar/qux')
        );

        $this->resource->setLinks('bar', $linksExpected);

        $resource = new Resource($this->repository);
        $this->resource->addResource('foo', $resource);

        $result = $this->resource->toArray();
        $this->assertTrue(isset($result['_links']['self']));
        $this->assertSame($result['_links']['self']['href'], 'foo');

        $this->assertTrue(isset($result['_links']['foo']));
        $this->assertSame($result['_links']['foo']['href'], 'bar');

        $this->assertTrue(isset($result['_links']['bar']));
        $this->assertSame($result['_links']['bar'][0]['href'], 'bar/foo');
        $this->assertSame($result['_links']['bar'][1]['href'], 'bar/qux');

        $this->assertTrue(isset($result['_embedded']['foo']));
        $this->assertTrue(is_array($result['_embedded']['foo']));
    }
}
