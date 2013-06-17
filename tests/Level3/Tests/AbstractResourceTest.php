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
use Level3\ResourceHub;
use Level3\Mocks\Mapper;
use Level3\Mocks\Resource;

use Teapot\StatusCode;

class AbstractResourceTest extends TestCase {
    public function testSetKeyAndGetKey()
    {
        $manager = new Resource();

        $manager->setKey('foo');
        $this->assertSame('foo', $manager->getKey()); 
    }

    public function testSetHubAndGetHub()
    {
        $manager = new Resource();
        $hub = $this->getHub();

        $manager->setHub($hub);
        $this->assertSame($hub, $manager->getHub()); 
    }

    public function testGetDescription()
    {
        $manager = new Resource();
        $this->assertSame('Foo', $manager->getDescription()); 
    }

    public function testCreate()
    {
        $hub = $this->getHub();
        $hub['mock'] = $hub->share(function ($c) {
            return new Resource();
        });

        $hub->boot();

        $resource = $hub['mock']->create(1);
        $this->assertInstanceOf('\Hal\Resource', $resource);

        $links = $resource->getLinks();
        $this->assertSame('/mock/1', $links['self']->getHref()); 
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCreateWithoutHub()
    {
        $manager = new Resource();
        $manager->create(1);
    }
}