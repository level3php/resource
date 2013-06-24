<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Messages\RequestFactory;
use Level3\Messages\Processors\ResourceAccessorWrapper;
use Mockery as m;

class ResourceAccessorWrapperTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_RESOURCE = 'Y';

    private $resourceAccessorMock;
    private $requestFactory;
    private $dummyRequest;
    private $dummyContent = array();

    private $resourceAccessorWrapper;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->resourceAccessorMock = m::mock('Level3\ResourceAccesor');
        $this->requestFactory = new RequestFactory();
        $this->dummyRequest = $this->createDummyRequest();
        $this->resourceAccessorWrapper = new ResourceAccessorWrapper($this->resourceAccessorMock);
    }


    public function testFind()
    {
        $this->resourceAccessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->resourceAccessorWrapper->find($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testGet()
    {
        $this->resourceAccessorMock->shouldReceive('get')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->resourceAccessorWrapper->get($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testPost()
    {
        $this->resourceAccessorMock->shouldReceive('post')
            ->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, $this->dummyContent)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->resourceAccessorWrapper->post($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testPut()
    {
        $this->resourceAccessorMock->shouldReceive('put')
            ->with(self::IRRELEVANT_KEY, $this->dummyContent)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->resourceAccessorWrapper->put($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testDelete()
    {
        $this->resourceAccessorMock->shouldReceive('delete')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->resourceAccessorWrapper->delete($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    private function createDummyRequest()
    {
        return $this->requestFactory->clear()
            ->withId(self::IRRELEVANT_ID)
            ->withKey(self::IRRELEVANT_KEY)
            ->withContent($this->dummyContent)
            ->create();
    }
}