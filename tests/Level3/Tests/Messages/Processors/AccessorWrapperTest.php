<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Hal\Resource;
use Level3\Messages\Exceptions\AttributeNotFound;
use Level3\Messages\Processors\AccessorWrapper;
use Level3\Messages\RequestFactory;
use Level3\Repository\Exception\Conflict;
use Level3\Repository\Exception\DataError;
use Level3\Repository\Exception\NoContent;
use Level3\Repository\Exception\NotFound;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class AccessorWrapperTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_CONTENT = 'Y';
    const IRRELEVANT_RESPONSE = 'YY';
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
        $this->accessorMock = m::mock('Level3\Accessor');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->formatterFactorymock = m::mock('Level3\Hal\Formatter\FormatterFactory');
        $this->parserFactoryMock = m::mock('Level3\Messages\Parser\ParserFactory');
        $this->requestMock = m::mock('Level3\Messages\Request');
        $this->accessorWrapper = new AccessorWrapper(
            $this->accessorMock, $this->responseFactoryMock, $this->formatterFactorymock, $this->parserFactoryMock
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
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $this->accesorFindShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE, $this->resourceMock);
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock);

        $response = $this->accessorWrapper->find($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    public function testFindWithPrepareResponseThrowingNotAcceptable()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $this->accesorFindShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE, $this->resourceMock);
        $this->shouldCreateResponseWithNotAcceptableAndReturn($this->requestMock, $this->resourceMock, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->find($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testFindThrowingBaseException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $exception = m::mock('Level3\Repository\Exception\BaseException');
        $this->accesorFindShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_RANGE,
            self::IRRELEVANT_RANGE,
            $exception
        );
        $this->shouldGenerateSpecificExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->find($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testFindThrowingException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $exception = m::mock('\Exception');
        $this->accesorFindShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_RANGE,
            self::IRRELEVANT_RANGE,
            $exception
        );
        $this->shouldGenerateGenericExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->find($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGet()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->accesorGetShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, $this->resourceMock);
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock);

        $response = $this->accessorWrapper->get($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    public function testGetWithPrepareResponseThrowingNotAcceptable()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->accesorGetShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, $this->resourceMock);
        $this->shouldCreateResponseWithNotAcceptableAndReturn($this->requestMock, $this->resourceMock, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGetThrowingBaseException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $exception = m::mock('Level3\Repository\Exception\BaseException');
        $this->accesorGetShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_ID,
            $exception
        );
        $this->shouldGenerateSpecificExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testGetThrowingException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $exception = m::mock('\Exception');
        $this->accesorGetShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_ID,
            $exception
        );
        $this->shouldGenerateGenericExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPut()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->shouldGetContentAsArrayAndReturn(array());
        $this->accesorPutShouldReceiveAndReturn(self::IRRELEVANT_KEY, array(), $this->resourceMock);
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock, 201);

        $response = $this->accessorWrapper->put($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    public function testPutWithPrepareResponseThrowingNotAcceptable()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->shouldGetContentAsArrayAndReturn(array());
        $this->accesorPutShouldReceiveAndReturn(self::IRRELEVANT_KEY, array(), $this->resourceMock);
        $this->shouldCreateResponseWithNotAcceptableAndReturn($this->requestMock, $this->resourceMock, self::IRRELEVANT_RESPONSE, 201);

        $response = $this->accessorWrapper->put($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPutThrowingBaseException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->shouldGetContentAsArrayAndReturn(array());
        $exception = m::mock('Level3\Repository\Exception\BaseException');
        $this->accesorPutShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            array(),
            $exception
        );
        $this->shouldGenerateSpecificExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->put($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPutThrowingException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $exception = m::mock('\Exception');
        $this->accesorGetShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            array(),
            $exception
        );
        $this->shouldGenerateGenericExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->get($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPost()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->shouldGetContentAsArrayAndReturn(array());
        $this->accesorPostShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array(), $this->resourceMock);
        $this->shouldCreateResponseWithAndReturn($this->requestMock, $this->resourceMock, $this->responseMock);

        $response = $this->accessorWrapper->post($this->requestMock);

        $this->assertThat($response, $this->equalTo($this->responseMock));
    }

    public function testPostWithPrepareResponseThrowingNotAcceptable()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->shouldGetContentAsArrayAndReturn(array());
        $this->accesorPostShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, array(), $this->resourceMock);
        $this->shouldCreateResponseWithNotAcceptableAndReturn($this->requestMock, $this->resourceMock, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->post($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPostThrowingBaseException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->shouldGetContentAsArrayAndReturn(array());
        $exception = m::mock('Level3\Repository\Exception\BaseException');
        $this->accesorPostShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_ID,
            array(),
            $exception
        );
        $this->shouldGenerateSpecificExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->post($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testPostThrowingException()
    {

        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->shouldGetContentAsArrayAndReturn(array());
        $exception = m::mock('\Exception');
        $this->accesorPostShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_ID,
            array(),
            $exception
        );
        $this->shouldGenerateGenericExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->post($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testDelete()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $this->accesorDeleteShouldReceiveAndReturn(self::IRRELEVANT_KEY, self::IRRELEVANT_ID, $this->resourceMock);
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array(), 200)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->delete($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testDeleteThrowingBaseException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveId(self::IRRELEVANT_ID);
        $exception = m::mock('Level3\Repository\Exception\BaseException');
        $this->accesorDeleteShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_ID,
            $exception
        );
        $this->shouldGenerateSpecificExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->delete($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function testDeleteThrowingException()
    {
        $this->requestShouldHaveKey(self::IRRELEVANT_KEY);
        $this->requestShouldHaveRange(self::IRRELEVANT_RANGE, self::IRRELEVANT_RANGE);
        $exception = m::mock('\Exception');
        $this->accesorDeleteShouldReceiveAndThrow(
            self::IRRELEVANT_KEY,
            self::IRRELEVANT_ID,
            $exception
        );
        $this->shouldGenerateGenericExceptionResponseWithAndReturn($exception, self::IRRELEVANT_RESPONSE);

        $response = $this->accessorWrapper->delete($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function requestShouldHaveRange($lowerBound, $upperBound)
    {
        $this->requestMock->shouldReceive('getRange')->withNoArgs()->once()->andReturn(array($lowerBound, $upperBound));
    }

    private function requestShouldHaveKey($key)
    {
        $this->requestMock->shouldReceive('getKey')->withNoArgs()->once()->andReturn($key);
    }

    private function requestShouldHaveId($id)
    {
        $this->requestMock->shouldReceive('getId')->withNoArgs()->once()->andReturn($id);
    }

    private function shouldCreateResponseWithAndReturn($request, $resource, $response, $status = 200)
    {
        $this->shouldPrepareResponseWithAndReturn($request, $resource, $response, $status);
    }

    private function shouldCreateResponseWithNotAcceptableAndReturn($request, $resource, $response, $status = 200)
    {
        $this->shouldPrepareResponseWithAndThrow($request, $resource, $status, 'Level3\Messages\Exceptions\NotAcceptable');
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array(), 406)->once()
            ->andReturn($response);
    }

    private function shouldPrepareResponseWithAndReturn($request, $resource, $response, $status = 200)
    {
        $this->shouldSetresourceFormatterWith($request, $resource);
        $this->responseFactoryMock->shouldReceive('create')->with($resource, $status)->once()->andReturn($response);
        $response->shouldReceive('prepare')->with($request)->once();
    }

    private function shouldPrepareResponseWithAndThrow($request, $resource, $statusCode, $exception)
    {
        $this->shouldSetresourceFormatterWith($request, $resource);
        $this->responseFactoryMock->shouldReceive('create')->with($resource, $statusCode)->once()->andThrow($exception);
    }

    private function shouldSetresourceFormatterWith($request, $resource)
    {
        $contentTypeArray = array(self::IRRELEVANT_CONTENT_TYPE);
        $formatterMock = m::mock('Level3\Hal\Formatter\Formatter');
        $request->shouldReceive('getAcceptableContentTypes')->withNoArgs()->once()
            ->andReturn($contentTypeArray);
        $this->formatterFactorymock->shouldReceive('create')->with($contentTypeArray)->once()->andReturn($formatterMock);
        $resource->shouldReceive('setFormatter')->with($formatterMock)->once();
    }

    private function accesorFindShouldReceiveAndReturn($key, $lowerBound, $upperBound, $return)
    {
        $this->accessorMock->shouldReceive('find')->with($key, $lowerBound, $upperBound)
            ->once()->andReturn($return);
    }

    private function accesorFindShouldReceiveAndThrow($key, $lowerBound, $upperBound, $exception)
    {
        $this->accessorMock->shouldReceive('find')->with($key, $lowerBound, $upperBound)
            ->once()->andThrow($exception);
    }

    private function shouldGenerateSpecificExceptionResponseWithAndReturn($exception, $return)
    {
        $exception->shouldReceive('getCode')->withNoArgs()->once()->andReturn(self::IRRELEVANT_CODE);
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array(), self::IRRELEVANT_CODE)
            ->once()->andReturn($return);
    }

    private function shouldGenerateGenericExceptionResponseWithAndReturn($exception, $return)
    {
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array('code' => 500), 500)
            ->once()->andReturn($return);
    }

    private function accesorGetShouldReceiveAndReturn($key, $id, $return)
    {
        $this->accessorMock->shouldReceive('get')->with($key, $id)
            ->once()->andReturn($return);
    }

    private function accesorGetShouldReceiveAndThrow($key, $id, $exception)
    {
        $this->accessorMock->shouldReceive('get')->with($key, $id)
            ->once()->andThrow($exception);
    }

    private function accesorPutShouldReceiveAndReturn($key, $data, $return)
    {
        $this->accessorMock->shouldReceive('put')->with($key, $data)
            ->once()->andReturn($return);
    }

    private function accesorPutShouldReceiveAndThrow($key, $data, $exception)
    {
        $this->accessorMock->shouldReceive('put')->with($key, $data)
            ->once()->andThrow($exception);
    }

    private function shouldGetContentAsArrayAndReturn($data)
    {
        $this->requestMock->shouldReceive('getContentType')->withNoArgs()->once()->andReturn(self::IRRELEVANT_CONTENT_TYPE);
        $parserMock = m::mock('Level3\Messages\Parser\Parser');
        $this->parserFactoryMock->shouldReceive('create')->with(self::IRRELEVANT_CONTENT_TYPE)->once()->andReturn($parserMock);
        $this->requestMock->shouldReceive('getContent')->withNoArgs()->once()->andReturn(self::IRRELEVANT_CONTENT);
        $parserMock->shouldReceive('parse')->with(self::IRRELEVANT_CONTENT)->once()->andreturn($data);
    }

    private function accesorPostShouldReceiveAndReturn($key, $id, $data, $return)
    {
        $this->accessorMock->shouldReceive('post')->with($key, $id, $data)
            ->once()->andReturn($return);
    }

    private function accesorPostShouldReceiveAndThrow($key, $id, $data, $exception)
    {
        $this->accessorMock->shouldReceive('post')->with($key, $id, $data)
            ->once()->andThrow($exception);
    }

    private function accesorDeleteShouldReceiveAndReturn($key, $id, $return)
    {
        $this->accessorMock->shouldReceive('delete')->with($key, $id)
            ->once()->andReturn($return);
    }

    private function accesorDeleteShouldReceiveAndThrow($key, $id, $exception)
    {
        $this->accessorMock->shouldReceive('delete')->with($key, $id)
            ->once()->andThrow($exception);
    }
}