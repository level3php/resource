<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Exceptions\Conflict;
use Level3\Messages\Processors\ExceptionHandler;
use Level3\Messages\Request;
use Mockery as m;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_RESPONSE = 'X';
    private $processorMock;
    private $responseFactoryMock;
    private $requestMock;
    private $exceptionHandler;

    public function setUp()
    {
        $this->markTestSkipped(
              'The MySQLi extension is not available.'
            );

        $this->processorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->requestMock = m::mock('Level3\Messages\Request');
        $this->exceptionHandler = new ExceptionHandler($this->processorMock, $this->responseFactoryMock);
    }

    /**
     * @dataProvider methods
     */
    public function testMethod($methodName)
    {
        $this->processorMock->shouldReceive($methodName)->with($this->requestMock)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->exceptionHandler->$methodName($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testMethodWithBaseExceptionAndDebug($methodName)
    {
        $exception = new Conflict();
        $this->processorMock->shouldReceive($methodName)->with($this->requestMock)->once()->andThrow($exception);
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')
            ->with($this->requestMock, m::subset(array('code'=>409, 'message' => '')), 409, true)
            ->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $this->exceptionHandler->enableDebug();
        $response = $this->exceptionHandler->$methodName($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testMethodWithExceptionAndDebug($methodName)
    {
        $exception = new \Exception();
        $this->processorMock->shouldReceive($methodName)->with($this->requestMock)->once()->andThrow($exception);
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')
            ->with($this->requestMock, m::subset(array('code'=>500, 'message' => '')), 500, true)
            ->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $this->exceptionHandler->enableDebug();
        $response = $this->exceptionHandler->$methodName($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testMethodWithExceptionLoggerAndDebug($methodName)
    {
        $exception = new \Exception();
        $this->processorMock->shouldReceive($methodName)->with($this->requestMock)->once()->andThrow($exception);
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')
            ->with($this->requestMock, m::subset(array('code'=>500, 'message' => '')), 500, true)
            ->once()->andReturn(self::IRRELEVANT_RESPONSE);
        $logger = m::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('log')->once();

        $this->exceptionHandler->enableDebug();
        $this->exceptionHandler->setLogger($logger);
        $response = $this->exceptionHandler->$methodName($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
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
