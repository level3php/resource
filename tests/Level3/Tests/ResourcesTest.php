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
use Level3\Resources;

class ResourcesTest extends TestCase
{
    public function testSetMapper()
    {
        $hub = $this->getHub();
        $hub['resources'] = $hub->share(function() {
            return new Resources();
        });

        $hub->boot();
        $r = $hub['resources']->find();
 
        $this->assertTrue(is_array($r));
        $this->assertSame(1, count($r));

        $resource = $r[0];
        $this->assertInstanceOf('Hal\Resource', $resource);

        $data = $resource->toArray();
        $this->assertSame('Resources', $data['name']);
        $this->assertSame('Available resources at this API', $data['description']);

        $this->assertFalse($resource->getSelf()->getHref());
    }
}