<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Messages\Processors\AccessorWrapper;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Level3\Tests\TestCase;

class AccessorWrapperTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_CONTENT = 'Y';
    const IRRELEVANT_RESPONSE = 'YY';
    const IRRELEVANT_SORT = 'ZZ';
    const IRRELEVANT_RANGE = 'YYY';
    const IRRELEVANT_CONTENT_TYPE = 'XXX';
    const IRRELEVANT_CODE = 'XY';
    private $accessorMock;
    private $responseFactoryMock;
    private $formatterFactorymock;
    private $parserFactoryMock;
    private $requestMock;
    private $resourceMock;
    private $responseMock;
    private $accessorWrapper;

    public function setUp()
    {
        $this->markTestSkipped(
              'The MySQLi extension is not available.'
            );
        
        $this->parametersMock = $this->createParametersMock();
        $this->accessorMock = m::mock('Level3\Accessor');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->formatterFactorymock = m::mock('Level3\Hal\Formatter\FormatterFactory');
        $this->parserFactoryMock = m::mock('Level3\Messages\Parser\ParserFactory');
        $this->requestMock = m::mock('Level3\Messages\Request');
        $this->accessorWrapper = new AccessorWrapper(
            $this->accessorMock, $this->responseFactoryMock, $this->parserFactoryMock
        );
        $this->resourceMock = m::mock('Level3\Hal\Resource');
        $this->responseMock = m::mock('Level3\Messages\Response');
    }

    public function tearDown()
    {
        unset($this->accessorMock);
        unset($this->responseFactoryMock);
        unset($this->formatterFactorymock);
        unset($this->parserFactoryMock);
        unset($this->requestMock);
        unset($this->accessorWrapper);
        unset($this->resourceMock);
        unset($this->responseMock);
    }

    public function testFind()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveParameters();
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $this->accesorFindShouldReceiveAndReturn(
            self::IRRELEVANT_KEY,
            $this->parametersMock,
            self::IRRELEVANT_SORT,
            self::IRRELEVANT_RANGE,
            self::IRRELEVANT_RANGE,
            array(),
            $this->resourceMock
        );
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock);
        $this->requestMock->shouldReceive('getCriteria')->withNoArgs()->once()->andReturn(array());
        $this->requestMock->shouldReceive('getSort')->withNoArgs()->once()->andReturn(self::IRRELEVANT_SORT);

        $response = $this->accessorWrapper->find($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    private function requestShouldHaveKey($key)
    {
        $this->requestMock->shouldReceive('getKey')->withNoArgs()->once()->andReturn($key);
    }

    private function requestShouldHaveRange($lowerBound, $upperBound)
    {
        $this->requestMock->shouldReceive('getRange')->withNoArgs()->once()->andReturn(array($lowerBound, $upperBound));
    }

    private function accesorFindShouldReceiveAndReturn($key, $parameters, $sort, $lowerBound, $upperBound, $criteria, $return)
    {
        $this->accessorMock->shouldReceive('find')->with($key, $parameters, $sort, $lowerBound, $upperBound, $criteria)
            ->once()->andReturn($return);
    }

    private function shouldCreateResponseWithAndReturn($request, $resource, $response, $status = 200)
    {
        $this->responseFactoryMock->shouldReceive('create')->with($request, $resource, $status)->once()->andReturn($response);
    }

    private function shouldPrepareResponseWithAndReturn($request, $resource, $response, $status = 200)
    {
        $response->shouldReceive('setContentType')->once();
        $this->shouldSetresourceFormatterWith($request, $resource);

        $response->shouldReceive('prepare')->with($request)->once();
    }

    private function shouldSetresourceFormatterWith($request, $resource)
    {
        $contentTypeArray = array(self::IRRELEVANT_CONTENT_TYPE);
        $formatterMock = m::mock('Level3\Hal\Formatter\Formatter');
        $request->shouldReceive('getAcceptableContentTypes')->withNoArgs()->once()
            ->andReturn($contentTypeArray);
        $this->formatterFactorymock->shouldReceive('create')->with($contentTypeArray)->once()->andReturn(
            $formatterMock
        );
        $formatterMock->shouldReceive('getContentType')->once();
        $resource->shouldReceive('setFormatter')->with($formatterMock)->once();
    }

    public function testGet()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveParameters();
        $this->accesorGetShouldReceiveAndReturn(self::IRRELEVANT_KEY, $this->parametersMock, $this->resourceMock);
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock);

        $response = $this->accessorWrapper->get($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    private function requestShouldHaveParameters()
    {
        $this->requestMock->shouldReceive('getParameters')->withNoArgs()->once()->andReturn($this->parametersMock);
    }

    private function accesorGetShouldReceiveAndReturn($key, $id, $return)
    {
        $this->accessorMock->shouldReceive('get')->with($key, $id)
            ->once()->andReturn($return);
    }

    public function testPut()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveParameters();
        $this->shouldGetContentAsArrayAndReturn(array());
        $this->accesorPutShouldReceiveAndReturn(self::IRRELEVANT_KEY, $this->parametersMock, array(), $this->resourceMock);
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock, 201);

        $response = $this->accessorWrapper->put($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    private function shouldGetContentAsArrayAndReturn($data)
    {
        $this->requestMock->shouldReceive('getContentType')->withNoArgs()->once()->andReturn(
            self::IRRELEVANT_CONTENT_TYPE
        );
        $parserMock = m::mock('Level3\Messages\Parser\Parser');
        $this->parserFactoryMock->shouldReceive('create')->with(self::IRRELEVANT_CONTENT_TYPE)->once()->andReturn(
            $parserMock
        );
        $this->requestMock->shouldReceive('getContent')->withNoArgs()->once()->andReturn(self::IRRELEVANT_CONTENT);
        $parserMock->shouldReceive('parse')->with(self::IRRELEVANT_CONTENT)->once()->andreturn($data);
    }

    private function accesorPutShouldReceiveAndReturn($key, $parameters, $data, $return)
    {
        $this->accessorMock->shouldReceive('put')->with($key, $parameters, $data)
            ->once()->andReturn($return);
    }

    public function testPost()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveParameters();
        $this->shouldGetContentAsArrayAndReturn(array());
        $this->accesorPostShouldReceiveAndReturn(
            self::IRRELEVANT_KEY,
            $this->parametersMock,
            array(),
            $this->resourceMock
        );
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock);

        $response = $this->accessorWrapper->post($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    private function accesorPostShouldReceiveAndReturn($key, $id, $data, $return)
    {
        $this->accessorMock->shouldReceive('post')->with($key, $id, $data)
            ->once()->andReturn($return);
    }

    public function testDelete()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveParameters();
        $this->accesorDeleteShouldReceiveAndReturn(self::IRRELEVANT_KEY, $this->parametersMock, $this->resourceMock);
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with($this->requestMock, array(), 200)->once()->andReturn(
            self::IRRELEVANT_RESPONSE
        );

        $response = $this->accessorWrapper->delete($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function accesorDeleteShouldReceiveAndReturn($key, $id, $return)
    {
        $this->accessorMock->shouldReceive('delete')->with($key, $id)
            ->once()->andReturn($return);
    }
}
