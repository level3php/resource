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
use Level3\Mocks\DummyResourceRepository;
use Level3\ResourceHub;
use Level3\Mocks\Mapper;
use Level3\Mocks\ResourceManager;
use Hal\Resource;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_HREF = 'XX';

    protected $resourceHubMock;

    //Make sure this method is really necessary
    protected function getHub()
    {
        $mapper = new Mapper;

        $hub = new ResourceHub();
        $hub->setMapper($mapper);

        return $hub;
    }
    //

    protected function resourceHubShouldHavePair($key, $value)
    {
        $this->resourceHubKeyShouldExist($key);
        $this->resourceHubMock->shouldReceive('offsetGet')->with($key)->once()->andReturn($value);
    }

    protected function resourceHubKeyShouldExist($key)
    {
        $this->resourceHubMock->shouldReceive('offsetExists')->with($key)->andReturn(true);
    }
}