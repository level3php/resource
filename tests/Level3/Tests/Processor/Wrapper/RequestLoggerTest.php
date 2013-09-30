<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\RequestLogger;
use Level3\Messages\Response;
use Psr\Log\LogLevel;
use Teapot\StatusCode;
use Exception;

use Mockery as m;

class RequestLoggerTest extends TestCase
{
    private $wrapper;

    public function setUp()
    {
        $this->loggerMock = m::mock('Psr\Log\LoggerInterface');
        $this->wrapper = new RequestLogger($this->loggerMock);
    }

    /**
     * @dataProvider provider
     */
    public function testExceptionHandling($method, $code, $level)
    {
        $attributes = $this->createParametersMock();
        $request = $this->createRequestMock($attributes);
        
        $this->loggerMock->shouldReceive($level)
            ->once()->andReturn(null);

        $response = new Response();
        $response->setStatusCode($code);

        $expected = $this->wrapper->$method(function($request) use ($response, $code) {
            return $response;
        }, $request);

        $this->assertSame($response, $expected);
    }

    public function provider()
    {
        return array(
            array('get', StatusCode::OK, LogLevel::INFO),
            array('get', StatusCode::NOT_FOUND, LogLevel::WARNING),
            array('get', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR),
            array('find', StatusCode::OK, LogLevel::INFO),
            array('find', StatusCode::NOT_FOUND, LogLevel::WARNING),
            array('find', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR),
            array('post', StatusCode::OK, LogLevel::INFO),
            array('post', StatusCode::NOT_FOUND, LogLevel::WARNING),
            array('post', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR),
            array('patch', StatusCode::OK, LogLevel::INFO),
            array('patch', StatusCode::NOT_FOUND, LogLevel::WARNING),
            array('patch', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR),
            array('put', StatusCode::OK, LogLevel::INFO),
            array('put', StatusCode::NOT_FOUND, LogLevel::WARNING),
            array('put', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR),
            array('delete', StatusCode::OK, LogLevel::INFO),
            array('delete', StatusCode::NOT_FOUND, LogLevel::WARNING),
            array('delete', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR)
        );
    }
}
