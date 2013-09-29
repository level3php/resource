<?php

namespace Level3\Tests;

use Teapot\StatusCode;
use Level3\Processor;
use Level3\Processor\Wrapper;
use Level3\Messages\Request;
use Level3\Messages\Response;

use Closure;
use RuntimeException;

class ProcessorTest extends TestCase
{
    private $processor;

    public function setUp()
    {
        $this->level3 = $this->createLevel3Mock();
        $this->level3->shouldReceive('getProcessorWrappers')
            ->withNoArgs()->andReturn(array(
                new WrapperMock('4', '*'),
                new WrapperMock('2', '/'),
                new WrapperMock('2', '/')
            ));

        $this->processor = new Processor($this->level3);
    }

    /**
     * @expectedException Level3\Exceptions\NotFound
     */
    public function testMissingRepository()
    {
        $attributes = $this->createParametersMock();
        $request = $this->createRequestMock($attributes, null, null);

        $exception = new RuntimeException();
        $this->level3->shouldReceive('getRepository')
            ->with(self::IRRELEVANT_KEY)->once()->andThrow($exception);
        
        $response = $this->processor->get($request);
    }

    /**
     * @dataProvider provider
     */
    public function testMethods($method, $repositoryMock, $attributes, $filters,  $content, $resource, $formatter, $statusCode)
    {
        $request = $this->createRequestMock($attributes, $filters, $formatter, $content);

        $repository = $this->$repositoryMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $repository);

        if ($filters) {
            $repository->shouldReceive($method)
                ->with($attributes, $filters)->once()->andReturn($resource);
        } else if ($content) {
            $repository->shouldReceive($method)
                ->with($attributes, $content)->once()->andReturn($resource);
        } else {
            $repository->shouldReceive($method)
                ->with($attributes)->once()->andReturn($resource);
        }
       
        $response = $this->processor->$method($request);

        $this->assertSame($statusCode, $response->getStatusCode());
        if ($statusCode != StatusCode::NO_CONTENT) {
            $this->assertSame($resource, $response->getResource());
            $this->assertSame($formatter, $response->getFormatter());
        }
    }

    public function provider()
    {
        return array(
            array(
                'find', 'createFinderMock', 
                $this->createParametersMock(), $this->createParametersMock(), null,
                $this->createResourceMock(), $this->createFormatterMock(), 
                StatusCode::OK
            ),
            array(
                'get', 'createGetterMock', 
                $this->createParametersMock(), null, null,
                $this->createResourceMock(), $this->createFormatterMock(), 
                StatusCode::OK
            ),
            array(
                'post', 'createPosterMock', 
                $this->createParametersMock(), null, array(true),
                $this->createResourceMock(), $this->createFormatterMock(), 
                StatusCode::CREATED
            ),
            array(
                'put', 'createPutterMock', 
                $this->createParametersMock(), null, array(true),
                $this->createResourceMock(), $this->createFormatterMock(), 
                StatusCode::OK
            ),
            array(
                'patch', 'createPatcherMock', 
                $this->createParametersMock(), null, array(true),
                $this->createResourceMock(), $this->createFormatterMock(), 
                StatusCode::OK
            ),
            array(
                'delete', 'createDeleterMock', 
                $this->createParametersMock(), null, null,
                null, null, 
                StatusCode::NO_CONTENT
            )
        );
    }

    protected function repositoryHubShouldHavePair($key, $value)
    {
        $this->level3->shouldReceive('getRepository')->with($key)->once()->andReturn($value);
    }
}

class WrapperMock implements Wrapper
{
    private $id;
    private $sign;

    public function __construct($id, $sign)
    {
        $this->id = $id;
        $this->sign = $sign;
    }

    public function find(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }
    
    public function get(Closure $execution, Request $request)
    {
        return $this->processRequest($execution ,$request);
    }

    public function post(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    public function put(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    public function patch(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    public function delete(Closure $execution, Request $request)
    {
        return $this->processRequest($execution, $request);
    }

    protected function processRequest(Closure $execution, Request $request)
    {
        $response = $execution($request);
        $base = $response->getStatusCode() - 200;

        if ($this->sign == '*') $code = $base * $this->id;
        if ($this->sign == '+') $code = $base + $this->id;
        if ($this->sign == '-') $code = $base - $this->id;
        if ($this->sign == '/') $code = $base / $this->id;  
    

        $response->setStatusCode($code + 200);
        return $response;
    }
}
