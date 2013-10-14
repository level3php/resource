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
        $request = $this->createResponseMock(); ;
        $execution = function($request) use ($request) { 
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
    public function testExceptionHandling($method, $code, $exception)
    {
        $attributes = $this->createParametersMock();
        $request = $this->createRequestMock(null, null, null);
        $this->processor->shouldReceive('error')
            ->once()->with($request, $exception);

        $response = $this->wrapper->$method(function($request) use ($exception) {
            $request->getKey();
            throw $exception;
        }, $request);
    }

    public function provider()
    {
        return array(
            array('get', StatusCode::NOT_FOUND, new NotFound()),
            array('get', StatusCode::INTERNAL_SERVER_ERROR, new Exception()),
            array('find', StatusCode::NOT_FOUND, new NotFound()),
            array('find', StatusCode::INTERNAL_SERVER_ERROR, new Exception()),
            array('post', StatusCode::NOT_FOUND, new NotFound()),
            array('post', StatusCode::INTERNAL_SERVER_ERROR, new Exception()),
            array('patch', StatusCode::NOT_FOUND, new NotFound()),
            array('patch', StatusCode::INTERNAL_SERVER_ERROR, new Exception()),
            array('put', StatusCode::NOT_FOUND, new NotFound()),
            array('put', StatusCode::INTERNAL_SERVER_ERROR, new Exception()),
            array('delete', StatusCode::NOT_FOUND, new NotFound()),
            array('delete', StatusCode::INTERNAL_SERVER_ERROR, new Exception()),
            array('options', StatusCode::INTERNAL_SERVER_ERROR, new Exception())
        );
    }
}
