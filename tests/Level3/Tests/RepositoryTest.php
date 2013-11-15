<?php

namespace Level3\Tests;

use Level3\Messages\Parameters;

class RepositoryTest extends TestCase
{
    private $repository;

    public function setUp()
    {
        $this->level3 = $this->createLevel3Mock();

        $this->repository = new RepositoryTestMock($this->level3);
    }

    public function testGetLevel3()
    {
        $this->assertSame($this->level3, $this->repository->getLevel3());
    }

    public function testGetKey()
    {
        $key = 'foo';
        $this->repository->setKey($key);
        $this->assertSame($key, $this->repository->getKey());
    }

    public function testGetURI()
    {
        $uri = 'qux';
        $key = 'foo';
        $method = 'bar';
        $attributes = $this->createParametersMock();

        $this->level3->shouldReceive('getURI')
            ->with($key, $method, $attributes)->once()
            ->andReturn($uri);

        $this->repository->setKey($key);
        $this->assertSame($uri, $this->repository->getURI($attributes, $method));
    }

    public function testGetDescription()
    {
        $description = 'foo bar';
        $this->assertSame($description, $this->repository->getDescription());
    }

    public function testCreateResource()
    {
        $uri = 'qux';
        $key = 'foo';
        $method = 'bar';
        $attributes = $this->createParametersMock();

        $this->level3->shouldReceive('getURI')
            ->with($key, null, $attributes)->once()
            ->andReturn($uri);

        $this->repository->setKey($key);
        $resource = $this->repository->createResource($attributes);

        $this->assertInstanceOf('Level3\Resource\Resource', $resource);
        $this->assertSame($uri, $resource->getURI());
    }
}

/**
 * foo bar
 */
class RepositoryTestMock
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
