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
use Level3\Hub;
use Mockery as m;

class HubTest extends TestCase
{
    public function testSetLevel3()
    {
        $level3 = m::mock('Level3\Level3');

        $hub = new Hub();
        $hub->setLevel3($level3);
    }

    public function testRegisterDefinition()
    {
        $repository = m::mock('Level3\Repository');
        $repository->shouldReceive('setKey')->once()->andReturn(['foo']);

        $hub = new Hub();
        $hub->registerDefinition('foo', function() use ($repository) {
            return $repository;
        });

        $this->assertSame($repository, $hub->get('foo'));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testRegisterDefinitionInvalidKey()
    {
        $hub = new Hub();
        $hub->registerDefinition('', function() {});
    }

    /**
     * @expectedException RuntimeException
     */
    public function testRegisterDefinitionInvalidClosureResult()
    {
        $hub = new Hub();
        $hub->registerDefinition('foo', function() {});
        $hub->get('foo');
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGetNotExistingDefinition()
    {
        $hub = new Hub();
        $hub->get('foo');
    }

    public function testGetKeys()
    {
        $hub = new Hub();
        $hub->registerDefinition('foo', function() {});

        $this->assertSame(['foo'], $hub->getKeys());
    }
}
