<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Hal\Resource;
use Level3\Messages\RequestFactory;
use Level3\Messages\Processors\AccessorWrapper;
use Mockery as m;
use Teapot\StatusCode;

class AccessorWrapperTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_CONTENT = 'Y';
    const IRRELEVANT_RESPONSE = 'YY';

    private $accessorMock;
    private $requestFactory;
    private $dummyRequest;
    private $responseFactoryMock;
    private $dummyResource;

    private $accessorWrapper;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
    }

    public function setUp()
    {
        $this->accessorMock = m::mock('Level3\Accessor');
        $this->requestFactory = new RequestFactory();
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->dummyRequest = $this->createDummyRequest();
        $this->accessorWrapper = new AccessorWrapper($this->accessorMock, $this->responseFactoryMock);
        $this->dummyResource = new Resource();
    }

    public function tearDown()
    {
        $this->accessorMock = null;
        $this->requestFactory = null;
        $this->responseFactoryMock = null;
        $this->dummyRequest = null;
        $this->accessorWrapper = null;
        $this->dummyResource = null;
    }


    public function testFind()
    {
        $this->accessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()
            ->andReturn($this->dummyResource);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(
            $this->dummyResource, StatusCode::OK, self::IRRELEVANT_RESPONSE
        );

        $result = $this->accessorWrapper->find($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @test
     * @dataProvider exceptionMapping
     */
    public function getShouldFailWithException($exception, $code)
    {
        $this->accessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()
            ->andThrow($exception);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(
            null, $code, self::IRRELEVANT_RESPONSE
        );

        $result = $this->accessorWrapper->find($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGet()
    {
        $this->accessorMock->shouldReceive('get')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()
            ->andReturn($this->dummyResource);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(
            $this->dummyResource, StatusCode::OK, self::IRRELEVANT_RESPONSE
        );

        $result = $this->accessorWrapper->get($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPost()
    {
        $this->accessorMock->shouldReceive('post')
            ->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array())->once()
            ->andReturn($this->dummyResource);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(
            $this->dummyResource, StatusCode::OK, self::IRRELEVANT_RESPONSE
        );

        $result = $this->accessorWrapper->post($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPut()
    {
        $this->accessorMock->shouldReceive('put')
            ->with(self::IRRELEVANT_KEY, array())->once()
            ->andReturn($this->dummyResource);
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(
            $this->dummyResource, StatusCode::CREATED, self::IRRELEVANT_RESPONSE
        );

        $result = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testDelete()
    {
        $this->accessorMock->shouldReceive('delete')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once();
        $this->responseFactoryCreateResponseShouldReceiveAndReturn(
            null, StatusCode::OK, self::IRRELEVANT_RESPONSE
        );

        $result = $this->accessorWrapper->delete($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function createDummyRequest()
    {
        return $this->requestFactory->clear()
            ->withId(self::IRRELEVANT_ID)
            ->withKey(self::IRRELEVANT_KEY)
            ->withContent(array())
            ->create();
    }

    private function responseFactoryCreateResponseShouldReceiveAndReturn($value, $statusCode, $return)
    {
        $this->responseFactoryMock->shouldReceive('createResponse')->with($value, $statusCode)->once()->andReturn($return);
    }

    public function exceptionMapping()
    {
        return array(
            array('Level3\Repository\Exception\Conflict', StatusCode::CONFLICT),
            array('Level3\Repository\Exception\DataError', StatusCode::BAD_REQUEST),
            array('Level3\Repository\Exception\NoContent', StatusCode::NO_CONTENT),
            array('Level3\Repository\Exception\NotFound', StatusCode::NOT_FOUND),
            array('\Exception', StatusCode::INTERNAL_SERVER_ERROR)
        );
    }
}