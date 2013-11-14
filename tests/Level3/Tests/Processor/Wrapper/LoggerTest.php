<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\Logger;
use Level3\Messages\Response;
use Psr\Log\LogLevel;
use Teapot\StatusCode;

use Mockery as m;

class LoggerTest extends TestCase
{
    private $wrapper;

    public function setUp()
    {
        $this->loggerMock = m::mock('Psr\Log\LoggerInterface');
        $this->wrapper = new Logger($this->loggerMock);
    }

    /**
     * @dataProvider provider
     */
    public function testExceptionHandling($method, $code, $level)
    {
        $request = $this->createRequestMock();

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
        return [
            ['get', StatusCode::OK, LogLevel::INFO],
            ['get', StatusCode::NOT_FOUND, LogLevel::WARNING],
            ['get', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR],
            ['find', StatusCode::OK, LogLevel::INFO],
            ['find', StatusCode::NOT_FOUND, LogLevel::WARNING],
            ['find', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR],
            ['post', StatusCode::OK, LogLevel::INFO],
            ['post', StatusCode::NOT_FOUND, LogLevel::WARNING],
            ['post', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR],
            ['patch', StatusCode::OK, LogLevel::INFO],
            ['patch', StatusCode::NOT_FOUND, LogLevel::WARNING],
            ['patch', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR],
            ['put', StatusCode::OK, LogLevel::INFO],
            ['put', StatusCode::NOT_FOUND, LogLevel::WARNING],
            ['put', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR],
            ['delete', StatusCode::OK, LogLevel::INFO],
            ['delete', StatusCode::NOT_FOUND, LogLevel::WARNING],
            ['delete', StatusCode::INTERNAL_SERVER_ERROR, LogLevel::ERROR],
            ['options', StatusCode::OK, LogLevel::INFO]
        ];
    }
}
