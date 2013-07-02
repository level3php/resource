<?php

namespace Level3\Tests\Messages;

use Level3\Messages\Request;
use Level3\Messages\ResponseGenerator;
use Level3\Repository\Exception\Conflict;
use Level3\Repository\Exception\DataError;
use Level3\Repository\Exception\NoContent;
use Level3\Repository\Exception\NotFound;

use Mockery as m;

class ResponseGeneratorTest extends \PHPUnit_Framework_TestCase
{
    const EXCEPTION_CODE = 23;
    const IRRELEVANT_EXCEPTION_MESSAGE = 'X';
    const IRRELEVANT_RESPONSE = 'XX';
    const IRRELEVANT_PATH_INFO = 'Y';
    const IRRELEVANT_KEY = 'YY';
    const IRRELEVANT_CONTENT = 'XY';
    const IRRELEVANT_CONTENT_TYPE = '3X';

    private $responseFactoryMock;
    private $parserFactoryMock;
    private $formatterFactoryMock;
    private $resourceFactoryMock;
    private $resourceMock;
    private $request;
    private $formatterMock;

    private $responseGenerator;

    public function setUp()
    {
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->parserFactoryMock = m::mock('Level3\Messages\Parser\ParserFactory');
        $this->formatterFactoryMock = m::mock('Level3\Hal\Formatter\FormatterFactory');
        $this->resourceFactoryMock = m::mock('Level3\Hal\ResourceFactory');
        $this->resourceMock = m::mock('Level3\Hal\Resource');
        $this->formatterMock = m::mock('Level3\Hal\Formatter\Formatter');

        $this->responseGenerator = new ResponseGenerator(
            $this->responseFactoryMock,
            $this->formatterFactoryMock,
            $this->resourceFactoryMock
        );
    }

    public function tearDown()
    {
        $this->responseFactoryMock = null;
        $this->parserFactoryMock = null;
        $this->formatterFactoryMock = null;
        $this->resourceFactoryMock = null;
        $this->resourceMock = null;
        $this->formatterMock = null;
        $this->responseGenerator = null;
    }

    public function testIsDebugEnabledShouldBeFalse()
    {
        $debugEnabled = $this->responseGenerator->isDebugEnabled();

        $this->assertThat($debugEnabled, $this->isFalse());
    }

    public function testEnableDebug()
    {
        $this->responseGenerator->enableDebug();
        $debugEnabled = $this->responseGenerator->isDebugEnabled();

        $this->assertThat($debugEnabled, $this->isTrue());
    }

    public function testDisableDebug()
    {
        $this->testEnableDebug();

        $this->responseGenerator->disableDebug();
        $debugEnabled = $this->responseGenerator->isDebugEnabled();

        $this->assertThat($debugEnabled, $this->isFalse());
    }

    /**
     * @dataProvider baseExceptions
     */
    public function testGenerateErrorResponseWithBaseException($exception, $code)
    {
        $this->resourceFactoryMock->shouldReceive('create')->with(null, array())->once()->andReturn($this->resourceMock);
        $formatterMock = m::mock('Level3\Hal\Formatter\Formatter');
        $this->formatterFactoryMock->shouldReceive('create')->with('application/hal+json')->once()->andReturn($formatterMock);
        $this->resourceMock->shouldReceive('setFormatter')->with($formatterMock);
        $this->responseFactoryMock->shouldReceive('create')->with($this->resourceMock, $code)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->responseGenerator->generateErrorResponse($exception);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function baseExceptions()
    {
        return array(
            array(new Conflict(), 409),
            array(new DataError(), 400),
            array(new NoContent(), 204),
            array(new NotFound(), 404)
        );
    }

    /**
     * @dataProvider debugAndExceptionData
     */
    public function testGenerateErrorResponseWithExceptionWith($debugEnabled, $exceptionData, $exception)
    {
        $this->resourceFactoryMock->shouldReceive('create')->with(null, m::subset($exceptionData))
            ->once()->andReturn($this->resourceMock);
        $formatterMock = m::mock('Level3\Hal\Formatter\Formatter');
        $this->formatterFactoryMock->shouldReceive('create')->with('application/hal+json')->once()->andReturn($formatterMock);
        $this->resourceMock->shouldReceive('setFormatter')->with($formatterMock);
        $this->responseFactoryMock->shouldReceive('create')->with($this->resourceMock, 500)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        if ($debugEnabled) $this->responseGenerator->enableDebug();
        $response = $this->responseGenerator->generateErrorResponse($exception);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function debugAndExceptionData()
    {
        return array(
            array(
                false,
                array(
                    'code' => 500,
                ),
                new \Exception()
            ),
            array(
                true,
                array(
                    'code' => 500,
                    'message' => self::IRRELEVANT_EXCEPTION_MESSAGE,
                ),
                new \Exception(self::IRRELEVANT_EXCEPTION_MESSAGE)
            )
        );
    }

    /**
     * @dataProvider validAcceptHeaders
     */
    public function testGenerateOKResponse($acceptHeader)
    {
        $this->shouldGenerateResponseWithRequestForResourceWith($acceptHeader, $this->resourceMock, 200, self::IRRELEVANT_RESPONSE);

        $response = $this->responseGenerator->generateOkResponse($this->request, $this->resourceMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function validAcceptHeaders()
    {
        return array(
            array('application/hal+json'),
            array('application/hal+xml')
        );
    }

    public function testGenerateOKResponseWithHeaderNotFound()
    {
        $this->request = new Request(self::IRRELEVANT_PATH_INFO, self::IRRELEVANT_KEY, array(), array(), self::IRRELEVANT_CONTENT );
        $this->formatterFactoryMock->shouldReceive('create')->with('application/hal+json')->once()->andReturn($this->formatterMock);
        $this->resourceMock->shouldReceive('setFormatter')->with($this->formatterMock);
        $this->responseFactoryMock->shouldReceive('create')->with($this->resourceMock, 200)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->responseGenerator->generateOkResponse($this->request, $this->resourceMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGenerateOKResponseWithNotAcceptable()
    {
        $this->shouldGenerateResponseWithRequestForResourceWith('not-acceptable', null, 406, self::IRRELEVANT_RESPONSE);

        $response = $this->responseGenerator->generateOkResponse($this->request, $this->resourceMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function shouldGenerateResponseWithRequestForResourceWith($format, $resource, $statusCode, $response)
    {
        $this->shouldGetFormatterForRequest($format, $this->formatterMock);
        $this->resourceMock->shouldReceive('setFormatter')->with($this->formatterMock);
        $this->responseFactoryMock->shouldReceive('create')->with($resource, $statusCode)->once()->andReturn($response);
    }

    private function shouldGetFormatterForRequest($format, $formatter)
    {
        $headers = array('accept' => $format);
        $this->createRequestWithHeaders($headers);
        $this->formatterFactoryMock->shouldReceive('create')->with($format)->once()->andReturn($formatter);
    }

    private function createRequestWithHeaders($headers)
    {
        $this->request = new Request(self::IRRELEVANT_PATH_INFO, self::IRRELEVANT_KEY, $headers, array(), self::IRRELEVANT_CONTENT);
    }
}