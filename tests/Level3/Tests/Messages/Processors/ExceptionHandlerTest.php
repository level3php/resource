<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Messages\Processors\ExceptionHandler;
use Level3\Messages\Request;
use Mockery as m;

class ExceptionHandlerTest extends 
{
    const IRRELEVANT_RESPONSE = 'X';
    private $processorMock;
    private $resourceFactoryMock;
    private $requestMock;
    private $exceptionHandler;

    public function __constructor()
    {
        $this->processorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->resourceFactoryMock = m::mock('Level3\Hal\ResourceFactory');
        $this->requestMock = m::mock('Level3\Messages\Request');
        $this->exceptionHandler = new ExceptionHandler($this->processorMock, $this->resourceFactoryMock);
    }

    /**
     * @dataProvider methods
     */
    public function testMethod($methodName)
    {
        $this->processorMock->shouldReceive($methodName)->with($this->requestMock)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->exceptionHandler->find($this->requestMock);

        $this->assertThat
    }

    /**
     * @dataProvider methods
     */
    public function testMethodWithExceptionAndDebug($methodName)
    {

    }

    /**
     * @dataProvider methods
     */
    public function testMethodWithLogger($methodName)
    {

    }

    public function methods()
    {
        return array(
            array('find'),
            array('get'),
            array('post'),
            array('put'),
            array('delete')
        );
    }
}