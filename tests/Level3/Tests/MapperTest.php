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
use Level3\Resource\Parameters;

use Mockery as m;

class MapperTest extends TestCase
{
    public function getMapperMock($constructor = array())
    {
        return m::mock(
            'Level3\Mapper[mapFinder,mapGetter,mapPoster,mapPutter,mapDeleter,mapPatcher,mapOptions]',
            $constructor
        );
    }

    public function testSetBaseURI()
    {
        $mapper = $this->getMapperMock();

        $expected = 'foo/';
        $mapper->setBaseURI($expected);

        $this->assertSame($expected, $mapper->getBaseURI());
    }

    public function testSetBaseURIEmpty()
    {
        $mapper = $this->getMapperMock();

        $expected = '';
        $mapper->setBaseURI('');

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
        $repository = new RepositoryMock($this->createLevel3Mock());
        $repository->setKey('foo');

        $hub =  m::mock('Level3\Hub');
        $hub->shouldReceive('get')->once()->with('foo')->andReturn($repository);
        $hub->shouldReceive('getKeys')->once()->andReturn(array('foo'));

        $repositoryKey = $repository->getKey();
        $mapper = $this->getMapperMock();
        $mapper->shouldReceive('mapGetter')->once()->with($repositoryKey, '/foo/{fooId}');
        $mapper->shouldReceive('mapPutter')->once()->with($repositoryKey, '/foo/{fooId}');
        $mapper->shouldReceive('mapPoster')->once()->with($repositoryKey, '/foo');
        $mapper->shouldReceive('mapDeleter')->once()->with($repositoryKey, '/foo/{fooId}');
        $mapper->shouldReceive('mapFinder')->once()->with($repositoryKey, '/foo');
        $mapper->shouldReceive('mapPatcher')->once()->with($repositoryKey, '/foo/{fooId}');
        $mapper->shouldReceive('mapOptions')->once()->with($repositoryKey, '/foo');
        $mapper->shouldReceive('mapOptions')->once()->with($repositoryKey, '/foo/{fooId}');

        $mapper->boot($hub);
    }

    public function testGetCurieURI()
    {
        $mapper = $this->getMapperMock();
        $this->assertSame(
            '/foo/{fooId}', 
            $mapper->getCurieURI('foo')
        );

        $this->assertSame(
            '/foo', 
            $mapper->getCurieURI('foo', 'Level3\Repository\Finder')
        );

        $this->assertSame(
            '/foo/{fooId}', 
            $mapper->getCurieURI('foo', 'Level3\Repository\Deleter')
        );

        $this->assertSame(
            '/foo/{fooId}/bar', 
            $mapper->getCurieURI('foo/bar', 'Level3\Repository\Finder')
        );

        $this->assertSame(
            '/foo/{fooId}/bar/{barId}', 
            $mapper->getCurieURI('foo/bar', 'Level3\Repository\Deleter')
        );

        $this->assertSame(
            '/foo/{fooId}/bar/{barId}/qux', 
            $mapper->getCurieURI('foo/bar/qux', 'Level3\Repository\Finder')
        );
        
        $this->assertSame(
            '/foo/{fooId}/bar/{barId}/qux/{quxId}', 
            $mapper->getCurieURI('foo/bar/qux', 'Level3\Repository\Deleter')
        );

        $mapper->setSkipCurieSegments(1);
        $this->assertSame(
            '/foo/bar/{barId}/qux/{quxId}', 
            $mapper->getCurieURI('foo/bar/qux', 'Level3\Repository\Deleter')
        );
    }

    public function testGetCurieURIUnknown()
    {
        $mapper = $this->getMapperMock();
        $this->assertNull( 
            $mapper->getCurieURI('foo', 'Level3\Repository\Foo')
        );
    }

    public function testGetURI()
    {
        $mapper = $this->getMapperMock();
        $this->assertSame(
            '/foo/1', 
            $mapper->getURI(
                'foo', 
                'Level3\Repository\Deleter', 
                new Parameters(array('fooId' => 1))
            )
        );
    }

    public function testGetURIWithOutParams()
    {
        $mapper = $this->getMapperMock();
        $this->assertSame(
            '/foo', 
            $mapper->getURI(
                'foo', 
                'Level3\Repository\Finder'
            )
        );
    }

    public function testGetMethods()
    {
        $repository = $this->createDeleterMock();
   
        $mapper = $this->getMapperMock();
        $this->assertSame(
            array('DELETE'), 
            $mapper->getMethods($repository)
        );
    }

    public function testGetMethodsAll()
    {
        $repository = new RepositoryMock($this->createLevel3Mock());
   
        $mapper = $this->getMapperMock();
        $this->assertSame(
            array('DELETE', 'GET', 'PATCH', 'POST', 'PUT'), 
            $mapper->getMethods($repository)
        );
    }
}


class RepositoryMock
    extends 
        \Level3\Repository 
    implements 
        \Level3\Repository\Getter, 
        \Level3\Repository\Finder, 
        \Level3\Repository\Putter, 
        \Level3\Repository\Poster, 
        \Level3\Repository\Deleter,
        \Level3\Repository\Patcher
{
    public function delete(Parameters $attributes) {}
    public function get(Parameters $attributes) {}
    public function post(Parameters $attributes, Array $data) {}
    public function put(Parameters $attributes, Array $data) {}
    public function patch(Parameters $attributes, Array $data) {}
    public function find(Parameters $attributes, Parameters $filters) {}

}