<?php

namespace Level3\Tests;

use Hal\Resource;
use Level3\Accessor;
use Level3\Repository\Exception\BaseException;
use Mockery as m;


class AccessorTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'Y';
    const IRRELEVANT_RESOURCE = '2X';

    private $accessor;
    private $repositoryHubMock;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->repositoryHubMock = m::mock('Level3\RepositoryHub');
        $this->accessor = new Accessor($this->repositoryHubMock);
    }

    public function testFind()
    {
        $finder = $this->createFinderMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $finder);
        $finder->shouldReceive('find')->with(0, 0, array())->once()->andReturn(array());

        $response = $this->accessor->find(self::IRRELEVANT_KEY, 0, 0, array());

        $this->assertThat($response, $this->equalTo(array()));
    }

    private function createFinderMock()
    {
        return m::mock('Level3\Repository\Finder');
    }

    public function testGet()
    {
        $getterMock = $this->createGetterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $getterMock);
        $getterMock->shouldReceive('get')->with(self::IRRELEVANT_ID)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->get(self::IRRELEVANT_KEY, self::IRRELEVANT_ID);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    private function createGetterMock()
    {
        return m::mock('Level3\Repository\Getter');
    }

    public function testPost()
    {
        $posterMock = $this->createPosterAndGetterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $posterMock);
        $posterMock->shouldReceive('post')->with(self::IRRELEVANT_ID, array())->once();
        $posterMock->shouldReceive('get')->with(self::IRRELEVANT_ID)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->post(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array());

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    private function createPosterAndGetterMock()
    {
        return m::mock('Level3\Repository\Poster, Level3\Repository\Getter');
    }

    public function testPut()
    {
        $putterMock = $this->createPutterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $putterMock);
        $putterMock->shouldReceive('put')->with(array())->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->put(self::IRRELEVANT_KEY, array());

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    private function createPutterMock()
    {
        return m::mock('Level3\Repository\Putter');
    }

    public function testDelete()
    {
        $deleterMock = $this->createDeleterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $deleterMock);
        $deleterMock->shouldReceive('delete')->with(self::IRRELEVANT_ID)->once();

        $response = $this->accessor->delete(self::IRRELEVANT_KEY, self::IRRELEVANT_ID);

        $this->assertThat($response, $this->equalTo(null));
    }

    private function createDeleterMock()
    {
        return m::mock('Level3\Repository\Deleter');
    }

    private function repositoryHubShouldHavePair($key, $value)
    {
        $this->repositoryHubMock->shouldReceive('get')->with($key)->once()->andReturn($value);
    }
}