<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Messages\RequestFactory;
use Mockery as m;
use Level3\Tests\Messages\Processors\RequestLogger;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Request;

class RequestLoggerTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_RESPONSE = 'XX';

    private $requestProcessorMock;
    private $loggerMock;
    private $dummyRequest;

    private $requestLogger;

    public function setUp()
    {
        $this->requestProcessorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->loggerMock = m::mock('Psr\Log\LoggerInterface');
        $this->dummyRequest = $this->createDummyRequest();

        $this->requestLogger = new \Level3\Messages\Processors\RequestLogger($this->requestProcessorMock, $this->loggerMock);
    }

    private function createDummyRequest()
    {
        $requestFactory = new RequestFactory();
        $symfonyRequest = new Request();
        return $requestFactory->clear()
            ->withSymfonyRequest($symfonyRequest)
            ->create();

    }

    public function tearDown()
    {
        unset($this->loggerMock);
        unset($this->requestProcessorMock);
    }

    public function testGetLogLevel()
    {
        $logLevel = $this->requestLogger->getLogLevel();

        $this->assertThat($logLevel, $this->equalTo(LogLevel::INFO));
    }

    public function testGetLogLevelAfterSettingIt()
    {
        $this->requestLogger->setLogLevel(LogLevel::ERROR);

        $logLevel = $this->requestLogger->getLogLevel();

        $this->assertThat($logLevel, $this->equalTo(LogLevel::ERROR));
    }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     */
    public function testSetLogLevelShouldFailDueToInvalidLevel()
    {
        $this->requestLogger->setLogLevel('invalid');
    }

    /**
     * @dataProvider methodMessage
     */
    public function testMethods($method, $message)
    {
        $logMessage = sprintf('%s %s - %s(%s)', $message, '/', 'Anonymous Credentials', 'anonymous');
        $this->loggerMock->shouldReceive('log')->with('info', $logMessage)->once();
        $this->requestProcessorMock->shouldReceive($method)->with($this->dummyRequest)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->requestLogger->$method($this->dummyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function methodMessage()
    {
        return array(
            array('find', 'FIND'),
            array('get', 'GET'),
            array('post', 'POST'),
            array('put', 'PUT'),
            array('delete', 'DELETE'),
        );
    }
}