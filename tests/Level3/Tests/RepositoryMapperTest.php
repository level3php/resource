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
use Level3\RepositoryHub;
use Mockery as m;

class RepositoryMapperTest extends TestCase
{
    public function getMapperMock($constructor = array())
    {
        return m::mock(
            'Level3\RepositoryMapper[getURI,mapFind,mapGet,mapPost,mapPut,mapDelete]',
            $constructor
        );
    }

    public function testGetReourceHub()
    {
        $hub =  m::mock('Level3\RepositoryHub');
        $mapper = $this->getMapperMock(array($hub));
        $this->assertInstanceOf('Level3\RepositoryHub', $mapper->getRepositoryHub());
    }

    public function testSetBaseURI()
    {
        $mapper = $this->getMapperMock();

        $expected = 'foo/';
        $mapper->setBaseURI($expected);

        $this->assertSame($expected, $mapper->getBaseURI());
    }

    public function testSetBaseURIWithoutTrallingSlash()
    {
        $mapper = $this->getMapperMock();

        $expected = 'foo/';
        $mapper->setBaseURI('foo');

        $this->assertSame($expected, $mapper->getBaseURI());
    }

    public function testBoot()
    {
        $repositoryMock = m::mock('Level3\Repository');

        $hub =  m::mock('Level3\RepositoryHub');
        $hub->shouldReceive('get')->once()->with('foo')->andReturn($repositoryMock);
        $hub->shouldReceive('getKeys')->once()->andReturn(array('foo'));
        $hub->shouldReceive('isFinder')->once()->andReturn(true);
        $hub->shouldReceive('isGetter')->once()->andReturn(true);
        $hub->shouldReceive('isPutter')->once()->andReturn(true);
        $hub->shouldReceive('isPoster')->once()->andReturn(true);
        $hub->shouldReceive('isDeleter')->once()->andReturn(true);

        $mapperMock = m::mock(
            'Level3\RepositoryMapper[getURI,mapFind,mapGet,mapPost,mapPut,mapDelete]',
            array($hub)
        );

        $repositoryMock->shouldReceive('setRepositoryMapper')->with($mapperMock);

        $mapperMock->shouldReceive('mapGet')->once()->with('foo', '/foo/{id}');
        $mapperMock->shouldReceive('mapPut')->once()->with('foo', '/foo');
        $mapperMock->shouldReceive('mapPost')->once()->with('foo', '/foo/{id}');
        $mapperMock->shouldReceive('mapDelete')->once()->with('foo', '/foo/{id}');
        $mapperMock->shouldReceive('mapFind')->once()->with('foo', '/foo');

        $mapperMock->boot();
    }
}