<?php

namespace Level3\Tests;

use Teapot\StatusCode;
use Level3\Processor;
use Level3\Repository\Exception\BaseException;
use Mockery as m;


class ProcessorTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_RESOURCE = '2X';

    private $processor;

    public function setUp()
    {
        $this->level3 = $this->createLevel3Mock();
        $this->processor = new Processor($this->level3);
    }
    
    /**
     * @dataProvider provider
     */
    public function testFind($method, $repositoryMock, $attributes, $filters, $content, $statusCode)
    {
        $formatter = $this->createFormatterMock();
        $request = $this->createRequestMock($attributes, $filters, $formatter, $content);

        $repository = $this->$repositoryMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $repository);
        
        $resource = $this->createResourceMock();

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
        $this->assertSame($resource, $response->getResource());
        $this->assertSame($formatter, $response->getFormatter());
    }

    public function provider()
    {
        return array(
            array(
                'find', 'createFinderMock', 
                $this->createParametersMock(), $this->createParametersMock(), null,
                StatusCode::OK
            ),
            array(
                'get', 'createGetterMock', 
                $this->createParametersMock(), null, null,
                StatusCode::OK
            ),
            array(
                'post', 'createPosterMock', 
                $this->createParametersMock(), null, array(true),
                StatusCode::CREATED
            ),
            array(
                'put', 'createPutterMock', 
                $this->createParametersMock(), null, array(true),
                StatusCode::OK
            ),
            array(
                'patch', 'createPatcherMock', 
                $this->createParametersMock(), null, array(true),
                StatusCode::OK
            ),
            array(
                'delete', 'createDeleterMock', 
                $this->createParametersMock(), null, null,
                StatusCode::OK
            )
        );
    }


/*



    public function testDelete()
    {
        $deleterMock = $this->createDeleterMock();
        $this->repositoryHubShouldHavePair(self::IRRELEVANT_KEY, $deleterMock);
        $deleterMock->shouldReceive('delete')->with($this->parametersMock)->once();

        $response = $this->processor->delete(self::IRRELEVANT_KEY, $this->parametersMock);

        $this->assertThat($response, $this->equalTo(null));
    }
*/

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

        if ($formatter) {
            $request->shouldReceive('getFormatter')
                ->withNoArgs()->once()->andReturn($formatter);
        }

        if ($content) {
            $request->shouldReceive('getContent')
                ->withNoArgs()->once()->andReturn($content);
        }

        return $request;
    }

    protected function repositoryHubShouldHavePair($key, $value)
    {
        $this->level3->shouldReceive('getRepository')->with($key)->once()->andReturn($value);
    }
}
