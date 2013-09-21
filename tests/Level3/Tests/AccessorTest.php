<?php

namespace Level3\Tests;

use Hal\Resource;
use Level3\Accessor;
use Level3\Repository\Exception\BaseException;
use Mockery as m;


class AccessorTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_RESOURCE = '2X';

    private $accessor;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->parametersMock = $this->createParametersMock();
        $this->repositoryHubMock = m::mock('Level3\Level3');
        $this->accessor = new Accessor($this->repositoryHubMock);
    }

    public function testFind()
    {
        $finder = $this->createFinderMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $finder);
        $finder->shouldReceive('find')->with($this->parametersMock, null, 0, 0, array())->once()->andReturn(array());

        $response = $this->accessor->find(self::IRRELEVANT_KEY, $this->parametersMock, null, 0, 0, array());

        $this->assertThat($response, $this->equalTo(array()));
    }


    public function testGet()
    {

        $getterMock = $this->createGetterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $getterMock);
        $getterMock->shouldReceive('get')->with($this->parametersMock)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->get(self::IRRELEVANT_KEY, $this->parametersMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }


    public function testPost()
    {
        $posterMock = $this->createPosterAndGetterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $posterMock);
        $posterMock->shouldReceive('post')->with($this->parametersMock, array())->once();
        $posterMock->shouldReceive('get')->with($this->parametersMock)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->post(self::IRRELEVANT_KEY, $this->parametersMock, array());

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
        $putterMock->shouldReceive('put')->with($this->parametersMock, array())->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->put(self::IRRELEVANT_KEY, $this->parametersMock, array());

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testPatch()
    {
        $patcherMock = $this->createPatcherMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $patcherMock);
        $patcherMock->shouldReceive('patch')->with($this->parametersMock, array())->once()->andReturn(self::IRRELEVANT_RESOURCE);
        $patcherMock->shouldReceive('get')->with($this->parametersMock)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->accessor->patch(self::IRRELEVANT_KEY, $this->parametersMock, array());

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testDelete()
    {
        $deleterMock = $this->createDeleterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $deleterMock);
        $deleterMock->shouldReceive('delete')->with($this->parametersMock)->once();

        $response = $this->accessor->delete(self::IRRELEVANT_KEY, $this->parametersMock);

        $this->assertThat($response, $this->equalTo(null));
    }

    protected function repositoryHubShouldHavePair($key, $value)
    {
        $this->repositoryHubMock->shouldReceive('getRepository')->with($key)->once()->andReturn($value);
    }
}
