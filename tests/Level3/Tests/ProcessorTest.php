<?php

namespace Level3\Tests;

use Teapot\StatusCode;
use Level3\Processor;
use Level3\Processor\Wrapper;
use Level3\Messages\Request;
use Level3\Messages\Response;
use Level3\Exceptions\NotFound;

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
                new WrapperMock(),
                new WrapperMock(),
                new WrapperMock()
            ));

        $this->processor = new Processor();
        $this->processor->setLevel3($this->level3);
    }

    /**
     * @expectedException Level3\Exceptions\NotFound
     */
    public function testMissingRepository()
    {
        $request = $this->createRequestMock(null, null, null);

        $this->level3->shouldReceive('getRepository')
            ->with(self::IRRELEVANT_KEY)->once()->andThrow(new RuntimeException());
        
        $response = $this->processor->get($request);
    }

    /**
     * @expectedException Level3\Exceptions\NotImplemented
     */
    public function testOptions()
    {
        $repository = $this->createFinderMock();
        $request = $this->createRequestMockSimple();
        $response = $this->processor->options($request);
    }

    /**
     * @dataProvider provider
     */
    public function testMethods($method, $repositoryMock, $attributes, $filters, $content, $resource, $formatter, $statusCode, $exception = null)
    {
        $repository = $this->$repositoryMock();

        if ($exception) {
            $request = $this->createRequestMock($attributes, $filters, $formatter, $repository, $content, false);
            $response = $this->processor->$method($request, $exception);
        } else {
            $request = $this->createRequestMock($attributes, $filters, $formatter, $repository, $content);

            $this->level3ShouldHavePair(self::IRRELEVANT_KEY, $repository);

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
        }

        $this->assertSame($statusCode, $response->getStatusCode());
        if ($statusCode != StatusCode::NO_CONTENT) {
            $this->assertSame($formatter, $response->getFormatter());
            if ($resource) {
                $this->assertSame($resource, $response->getResource());
            }
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
            ),
            array(
                'error', 'createDeleterMock', 
                null, null, null,
                null, $this->createFormatterMock(), 
                StatusCode::INTERNAL_SERVER_ERROR, new \Exception
            ),
            array(
                'error', 'createDeleterMock', 
                null, null, null,
                null, $this->createFormatterMock(), 
                StatusCode::NOT_FOUND, new NotFound
            )
        );
    }

    protected function level3ShouldHavePair($key, $repository)
    {
        $this->level3->shouldReceive('getRepository')
            ->with($key)->once()->andReturn($repository);
    }
}

class WrapperMock extends Wrapper
{
    private $id;
    private $sign;

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $response = $execution($request);
        
        return $response;
    }
}
