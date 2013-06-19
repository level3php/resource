<?php

namespace Level3\Tests;

use Hal\Resource;
use Level3\Mocks\DummyResourceRepository;
use Level3\ResourceAccesor;
use Level3\ResourceRepository\Exception\BaseException;
use Teapot\StatusCode;
use Mockery as m;

class ResourceAccessorTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'Y';
    const IRRELEVANT_RESPONSE = 'XX';

    private $responseFactoryMock;
    private $resourceAccessor;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
        $this->resourceHubMock = m::mock('Level3\ResourceHub');
    }

    public function setUp()
    {
        $this->responseFactoryMock = m::mock('Level3\ResponseFactory');
        $this->resourceHubMock = m::mock('Level3\ResourceHub');
        $this->resourceAccessor = new ResourceAccesor($this->resourceHubMock, $this->responseFactoryMock);
    }

    /**
     * @test
     */
    public function shouldFind()
    {
        $findInterface = $this->createFinderMock();
        $findInterface->shouldReceive('find')->withNoArgs()->once()->andReturn(array());
        $this->resourceHubShouldHavePair(self::IRRELEVANT_KEY, $findInterface);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(array(), StatusCode::OK, self::IRRELEVANT_RESPONSE);

        $response = $this->resourceAccessor->find(self::IRRELEVANT_KEY);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @test
     * @dataProvider exceptionMapping
     */
    public function findShouldFailWithBaseException($exception, $code)
    {
        $finderMock = $this->createFinderMock();
        $finderMock->shouldReceive('find')->andThrow($exception);
        $this->resourceHubShouldHavePair(self::IRRELEVANT_KEY, $finderMock);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(null, $code, self::IRRELEVANT_RESPONSE);

        $response = $this->resourceAccessor->find(self::IRRELEVANT_KEY);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function responseFactoryCreateResponseShouldReceiveAndReturn($value, $statusCode, $return)
    {
        $this->responseFactoryMock->shouldReceive('createResponse')->with($value, $statusCode)->once()->andReturn($return);
    }

    private function createResourceRepositoryDummy()
    {
        return new DummyResourceRepository();
    }

    public function resourceRepositoryFindShouldThrow($exception)
    {
        $this->resourceRepositoryMock->shouldReceive('find')->withNoArgs()->once()->andThrow($exception);
    }

    private function createFinderMock()
    {
        return m::mock('Level3\ResourceRepository\FindInterface');
    }

    public function exceptionMapping()
    {
        return array(
            array('Level3\ResourceRepository\Exception\Conflict', StatusCode::CONFLICT),
            array('Level3\ResourceRepository\Exception\DataError', StatusCode::BAD_REQUEST),
            array('Level3\ResourceRepository\Exception\NoContent', StatusCode::NO_CONTENT),
            array('Level3\ResourceRepository\Exception\NotFound', StatusCode::NOT_FOUND),
        );
    }
}