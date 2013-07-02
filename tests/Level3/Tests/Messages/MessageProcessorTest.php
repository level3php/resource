<?php

namespace Level3\Tests\Messages;

use Level3\Messages\MessageProcessor;
use Mockery as m;

class MessageProcessorTest  extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_RESPONSE = 'X';

    private $requestParserMock;
    private $responseGeneratorMock;
    private $messageProcessor;

    public function setUp()
    {
        $this->requestParserMock = m::mock('Level3\Messages\RequestParser');
        $this->responseGeneratorMock = m::mock('Level3\Messages\ResponseGenerator');

        $this->messageProcessor = new MessageProcessor($this->requestParserMock, $this->responseGeneratorMock);
    }

    public function testGenerateErrorResponse()
    {
        $exception = new \Exception();
        $this->responseGeneratorMock->shouldReceive('generateErrorResponse')->with($exception)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->messageProcessor->generateErrorResponse($exception);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGenerateOKResponse()
    {
        $requestMock = $this->createRequestMock();
        $resourceMock = m::mock('Level3\Hal\Resource');
        $this->responseGeneratorMock->shouldReceive('generateOKResponse')->with($requestMock, $resourceMock)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->messageProcessor->generateOKResponse($requestMock, $resourceMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGenerateDeletedResponse()
    {
        $this->responseGeneratorMock->shouldReceive('generateDeletedResponse')->withNoArgs()->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->messageProcessor->generateDeletedResponse();

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGetRequestContentAsArray()
    {
        $requestMock = $this->createRequestMock();
        $this->requestParserMock->shouldReceive('getRequestContentAsArray')->with($requestMock)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->messageProcessor->getRequestContentAsArray($requestMock);

        $this->assertThat($response,$this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function createRequestMock()
    {
        return m::mock('Level3\Messages\Request');
    }
}