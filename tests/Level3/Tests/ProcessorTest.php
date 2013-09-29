<?php

namespace Level3\Tests;

use Teapot\StatusCode;
use Level3\Processor;
use Level3\Repository\Exception\BaseException;
use Mockery as m;

use RuntimeException;

class ProcessorTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_RESOURCE = '2X';

    private $processor;

    public function setUp()
    {
        $this->level3 = $this->createLevel3Mock();
        $this->level3->shouldReceive('getProcessorWrappers')
            ->withNoArgs()->andReturn(array());

        $this->processor = new Processor($this->level3);
    }

    /**
      * @expectedException Level3\Exceptions\NotFound
      */
    public function testMissingRepository()
    {
        $formatter = $this->createFormatterMock();
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

    protected function createRequestMock(
        $attributes = null, $filters = null, $formatter = null, $content = null)
    {
        $request = parent::createRequestMock();
        $request->shouldReceive('getKey')
            ->withNoArgs()->once()->andReturn(self::IRRELEVANT_KEY);
        
        if ($attributes) {
            $request->shouldReceive('getAttributes')
                ->withNoArgs()->once()->andReturn($attributes);
        }

        if ($filters) {
            $request->shouldReceive('getFilters')
                ->withNoArgs()->once()->andReturn($filters);
        }

        if ($content) {
            $request->shouldReceive('getContent')
                ->withNoArgs()->once()->andReturn($content);
        }

        if ($formatter) {
            $request->shouldReceive('getFormatter')
                ->withNoArgs()->once()->andReturn($formatter);
        }

        return $request;
    }

    protected function repositoryHubShouldHavePair($key, $value)
    {
        $this->level3->shouldReceive('getRepository')->with($key)->once()->andReturn($value);
    }
}
