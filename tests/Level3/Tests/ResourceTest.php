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
use Mockery as m;

class ResourceTest extends TestCase
{
    public function setUp()
    {
        $this->repository = $this->createRepositoryMock();
        $this->resource = new Resource($this->repository);
    }

    public function testAddLink()
    {
        $link = $this->createLinkMock();
        $this->resource->addLink('foo', $link);

        $links = $this->resource->getLinks();
        $this->assertSame($link, $links['foo'][0]);
    }

    public function testLinkResource()
    {
        $linkedResource = new Resource($this->repository);
        $this->repository->shouldReceive('getResourceURI')
            ->once()->with($linkedResource, Resource::DEFAULT_INTERFACE_METHOD)
            ->andReturn('foo');

        $this->resource->linkResource('foo', $linkedResource);
        $links = $this->resource->getLinks();
        $this->assertInstanceOf('Level3\Resource\Link', $links['foo'][0]);
    }

    public function testAddResource()
    {
        $resource = new Resource($this->repository);

        $this->resource->addResource('foo', $resource);
        $resources = $this->resource->getResources();
        $this->assertSame($resource, $resources['foo'][0]);
    }

    public function testSetData()
    {
        $this->resource->setData(array('foo' => 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->resource->getData());
    }

    public function testSetAtributes()
    {
        $attributes = $this->createParametersMock();

        $this->resource->setAttributes($attributes);
        $this->assertSame($attributes, $this->resource->getAttributes());
    }
}
