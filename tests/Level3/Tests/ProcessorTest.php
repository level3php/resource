<?php

namespace Level3\Tests;

use Hal\Resource;
use Level3\Processor;
use Level3\Repository\Exception\BaseException;
use Mockery as m;


class ProcessorTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_RESOURCE = '2X';

    private $processor;

    public function setUp()
    {
        $this->level3 = $this->createLevel3Mock();
        $this->processor = new Processor($this->level3);
    }

    public function testFind()
    {
        $attributes = $this->createParametersMock();
        $filters = $this->createParametersMock();
        $formatter = $this->createFormatterMock();
        $request = $this->createRequestMock($attributes, $filters, $formatter);
        
        $finder = $this->createFinderMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $finder);
        
        $resource = $this->createResourceMock();
        $finder->shouldReceive('find')
            ->with($attributes, $filters)->once()->andReturn($resource);

        $response = $this->processor->find($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($resource, $response->getResource());
        $this->assertSame($formatter, $response->getFormatter());
    }


    public function testGet()
    {

        $getterMock = $this->createGetterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $getterMock);
        $getterMock->shouldReceive('get')->with($this->parametersMock)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->processor->get(self::IRRELEVANT_KEY, $this->parametersMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }


    public function testPost()
    {
        $posterMock = $this->createPosterAndGetterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $posterMock);
        $posterMock->shouldReceive('post')->with($this->parametersMock, array())->once();
        $posterMock->shouldReceive('get')->with($this->parametersMock)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->processor->post(self::IRRELEVANT_KEY, $this->parametersMock, array());

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

        $response = $this->processor->put(self::IRRELEVANT_KEY, $this->parametersMock, array());

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testPatch()
    {
        $patcherMock = $this->createPatcherMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $patcherMock);
        $patcherMock->shouldReceive('patch')->with($this->parametersMock, array())->once()->andReturn(self::IRRELEVANT_RESOURCE);
        $patcherMock->shouldReceive('get')->with($this->parametersMock)->once()->andReturn(self::IRRELEVANT_RESOURCE);

        $response = $this->processor->patch(self::IRRELEVANT_KEY, $this->parametersMock, array());

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testDelete()
    {
        $deleterMock = $this->createDeleterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $deleterMock);
        $deleterMock->shouldReceive('delete')->with($this->parametersMock)->once();

        $response = $this->processor->delete(self::IRRELEVANT_KEY, $this->parametersMock);

        $this->assertThat($response, $this->equalTo(null));
    }

    protected function createRequestMock($attributes, $filters, $formatter)
    {
        $request = parent::createRequestMock();
        $request->shouldReceive('getKey')
            ->withNoArgs()->once()->andReturn(self::IRRELEVANT_KEY);
        $request->shouldReceive('getAttributes')
            ->withNoArgs()->once()->andReturn($attributes);
        $request->shouldReceive('getFilters')
            ->withNoArgs()->once()->andReturn($filters);
        $request->shouldReceive('getFormatter')
            ->withNoArgs()->once()->andReturn($formatter);

        return $request;
    }


    protected function repositoryHubShouldHavePair($key, $value)
    {
        $this->level3->shouldReceive('getRepository')->with($key)->once()->andReturn($value);
    }
}
