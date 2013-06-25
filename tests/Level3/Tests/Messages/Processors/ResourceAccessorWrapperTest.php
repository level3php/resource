<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Messages\RequestFactory;
use Level3\Messages\Processors\AccessorWrapper;
use Mockery as m;

class AccessorWrapperTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_RESOURCE = 'Y';

    private $accessorMock;
    private $requestFactory;
    private $dummyRequest;
    private $dummyContent = array();

    private $accessorWrapper;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->accessorMock = m::mock('Level3\Accessor');
        $this->requestFactory = new RequestFactory();
        $this->dummyRequest = $this->createDummyRequest();
        $this->accessorWrapper = new AccessorWrapper($this->accessorMock);
    }


    public function testFind()
    {
        $this->accessorMock->shouldReceive('find')->with(self::IRRELEVANT_KEY)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->accessorWrapper->find($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testGet()
    {
        $this->accessorMock->shouldReceive('get')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->accessorWrapper->get($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testPost()
    {
        $this->accessorMock->shouldReceive('post')
            ->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, $this->dummyContent)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->accessorWrapper->post($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testPut()
    {
        $this->accessorMock->shouldReceive('put')
            ->with(self::IRRELEVANT_KEY, $this->dummyContent)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->accessorWrapper->put($this->dummyRequest);

        $this->assertThat($result, $this->equalTo(self::IRRELEVANT_RESOURCE));
    }

    public function testDelete()
    {
        $this->accessorMock->shouldReceive('delete')->with(self::IRRELEVANT_KEY, self::IRRELEVANT_ID)->once()
            ->andReturn(self::IRRELEVANT_RESOURCE);

        $result = $this->accessorWrapper->delete($this->dummyRequest);

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