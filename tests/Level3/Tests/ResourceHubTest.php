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
use Level3\Mocks\ResourceDriver;

use Teapot\StatusCode;

class ResourceHubTest extends TestCase {
    private function getHub()
    {
        $mapper = new Mapper;

        $hub = new ResourceHub();
        $hub->setMapper($mapper);

        $hub['mock'] = $hub->share(function ($c) {
            return new ResourceDriver();
        });

        return $hub; 
    }

    public function testSetMapper()
    {
        $mapper = new Mapper;
        $hub = new ResourceHub();

        $hub->setMapper($mapper);
        $this->assertSame($mapper, $hub->getMapper());
    }

    public function testSetBaseURI()
    {
        $hub = $this->getHub();

        $hub->setBaseURI('/');
        $this->assertSame('/', $hub->getBaseURI());
    }

    public function testSetBaseURINotTrailing()
    {
        $hub = $this->getHub();

        $hub->setBaseURI('/foo');
        $this->assertSame('/foo/', $hub->getBaseURI());
    }

    public function testBootAndGetURI()
    {
        $hub = $this->getHub();
        $hub->boot();

        $this->assertSame('/mock', $hub->getURI('mock', 'list'));
        $this->assertSame('/mock/{id}', $hub->getURI('mock', 'get'));
        $this->assertSame('/mock/{id}', $hub->getURI('mock', 'post'));
        $this->assertSame('/mock', $hub->getURI('mock', 'put'));
        $this->assertSame('/mock/{id}', $hub->getURI('mock', 'delete'));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testBootWrongDriverNonObject()
    {
        $hub = $this->getHub();
        $hub['nonObject'] = $hub->share(function ($c) {
            return null;
        });

        $hub->boot();
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testBootWrongDriverNonResourceDriver()
    {
        $hub = $this->getHub();
        $hub['nonResourceDriver'] = $hub->share(function ($c) {
            return (object)1;
        });

        $hub->boot();
    }
}