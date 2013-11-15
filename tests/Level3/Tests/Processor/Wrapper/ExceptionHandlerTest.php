<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\ExceptionHandler;
use Level3\Exceptions\NotFound;
use Teapot\StatusCode;
use Exception;

class ExceptionHandlerTest extends TestCase
{
    private $wrapper;

    public function setUp()
    {
        $this->processor = $this->createProcessorMock();

        $this->level3 = $this->createLevel3Mock();
        $this->level3->shouldReceive('getProcessor')->andReturn($this->processor);

        $this->wrapper = new ExceptionHandler();
        $this->wrapper->setLevel3($this->level3);
    }

    public function testErrorAuthentication()
    {
        $request = $this->createResponseMock();
        $execution = function ($request) use ($request) {
            return $request;
        };

        $request = $this->createRequestMockSimple();
        $wrapper = new ExceptionHandler();

        $this->assertInstanceOf(
            'Level3\Messages\Response',
            $wrapper->error($execution, $request)
        );
    }

    /**
     * @dataProvider provider
     */
    public function testExceptionHandling($method, $exception)
    {
        $request = $this->createRequestMock(null, null, null);
        $this->processor->shouldReceive('error')
            ->once()->with($request, $exception);

        $this->wrapper->$method(function($request) use ($exception) {
            $request->getKey();
            throw $exception;
        }, $request);
    }

    public function provider()
    {
        return [
            ['get', new NotFound()],
            ['get', new Exception()],
            ['find', new NotFound()],
            ['find', new Exception()],
            ['post', new NotFound()],
            ['post', new Exception()],
            ['patch', new NotFound()],
            ['patch', new Exception()],
            ['put', new NotFound()],
            ['put', new Exception()],
            ['delete', new NotFound()],
            ['delete', new Exception()],
            ['options', new Exception()]
        ];
    }
}
