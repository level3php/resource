<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests;

use Level3\Resource;
use Level3\Messages\Response;
use Level3\Formatter\HALJsonFormatter;
use Level3\Exceptions\NotAcceptable;

use Teapot\StatusCode;
use Mockery as m;

class ResponseTest extends TestCase
{
    public function testCreateFromResourceBasic()
    {
        $resource = $this->createResourceMock(false);
        $resource->shouldReceive('getId')->once()->andReturn(null);
        $resource->shouldReceive('getCache')->once()->andReturn(null);
        $resource->shouldReceive('getLastUpdate')->once()->andReturn(null);

        $this->helperTestCreateFromResource($resource);
    }

    public function testCreateFromResourceWithId()
    {
        $resource = $this->createResourceMock(false);
        $resource->shouldReceive('getId')->once()->andReturn('foo');
        $resource->shouldReceive('getCache')->once()->andReturn(null);
        $resource->shouldReceive('getLastUpdate')->once()->andReturn(null);

        $response = $this->helperTestCreateFromResource($resource);

        $this->assertSame('"foo"', $response->getEtag());
    }

    public function testCreateFromResourceWithLastUpdate()
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));

        $resource = $this->createResourceMock(false);
        $resource->shouldReceive('getId')->once()->andReturn(null);
        $resource->shouldReceive('getCache')->once()->andReturn(null);
        $resource->shouldReceive('getLastUpdate')->once()->andReturn($date);

        $response = $this->helperTestCreateFromResource($resource);

        $this->assertSame($date->format('D, d M Y H:i:s').' GMT', $response->getLastModified()->format('D, d M Y H:i:s').' GMT');
    }

    public function testCreateFromResourceWithCache()
    {
        $cache = 100;

        $resource = $this->createResourceMock(false);
        $resource->shouldReceive('getId')->once()->andReturn(null);
        $resource->shouldReceive('getCache')->once()->andReturn($cache);
        $resource->shouldReceive('getLastUpdate')->once()->andReturn(null);

        $response = $this->helperTestCreateFromResource($resource);

        $this->assertSame(time()+100, $response->getExpires()->getTimestamp());
    }

    protected function helperTestCreateFromResource($resource)
    {
        $formatter = $this->createFormatterMock();
        $request = $this->createRequestMockSimple(false);
        $request->shouldReceive('getFormatter')
                ->withNoArgs()->once()->andReturn($formatter);

        $response = Response::createFromResource($request, $resource);

        $this->assertSame(StatusCode::OK, $response->getStatusCode());
        $this->assertSame($resource, $response->getResource());
        $this->assertSame($formatter, $response->getFormatter());

        return $response;
    }

    public function testCreateFromException()
    {
        $formatter = $this->createFormatterMock();
        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getFormatter')
                ->withNoArgs()->once()->andReturn($formatter);

        $exception = new \Exception('foo');
        $response = Response::createFromException($request, $exception);

        $this->assertSame(StatusCode::INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $resource = $response->getResource();
        $this->assertInstanceOf('Level3\Resource', $resource);
        $this->assertSame($formatter, $response->getFormatter());

        $data = $resource->getData();
        $this->assertSame('Exception', $data['type']);
        $this->assertSame('foo', $data['message']);
    }

    public function testCreateFromHTTPException()
    {
        $formatter = $this->createFormatterMock();
        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getFormatter')
                ->withNoArgs()->once()->andReturn($formatter);

        $exception = new NotAcceptable('foo');
        $response = Response::createFromException($request, $exception);

        $this->assertSame(StatusCode::NOT_ACCEPTABLE, $response->getStatusCode());

        $resource = $response->getResource();
        $this->assertInstanceOf('Level3\Resource', $resource);
        $this->assertSame($formatter, $response->getFormatter());

        $data = $resource->getData();
        $this->assertSame('NotAcceptable', $data['type']);
        $this->assertSame('foo', $data['message']);
    }

    public function testSetResource()
    {
        $resource = $this->createResourceMock();
        $response = new Response();
        $response->setResource($resource);
        $this->assertSame($resource, $response->getResource());
    }

    public function testSetStatus()
    {
        $response = new Response();
        $response->setStatusCode(StatusCode::NOT_FOUND);
        $this->assertSame(StatusCode::NOT_FOUND, $response->getStatusCode());
    }

    public function testAddHeader()
    {
        $response = new Response();

        $response->addHeader('foo', 'bar');
        $fooHeader = $response->getHeader('foo');

        $this->assertThat($fooHeader, $this->equalTo('bar'));
    }

    public function testAddHeaderTwiceAndGetHeaders()
    {
        $response = new Response();
        $response->addHeader('foo', 'bar');
        $response->addHeader('foo', 'crap');

        $fooHeaders = $response->getHeaders('foo');

        $this->assertThat($fooHeaders, $this->equalTo(['bar', 'crap']));
    }

    public function testAddHeaderTwiceAndGetHeader()
    {
        $response = new Response();
        $response->addHeader('foo', 'bar');
        $response->addHeader('foo', 'crap');

        $fooHeaders = $response->getHeader('foo');

        $this->assertThat($fooHeaders, $this->equalTo('bar'));
    }

    public function testSetHeaderTwiceAndGetHeaders()
    {
        $response = new Response();
        $response->setHeader('foo', 'bar');
        $response->setHeader('foo', 'qux');

        $fooHeder = $response->getHeaders('foo');

        $this->assertThat($fooHeder, $this->equalTo(['qux']));
    }

    public function testSetHeaderTwiceAndGetHeader()
    {
        $response = new Response();
        $response->setHeader('foo', 'bar');
        $response->setHeader('foo', 'qux');

        $fooHeder = $response->getHeader('foo');

        $this->assertThat($fooHeder, $this->equalTo('qux'));
    }

    public function testGetContent()
    {
        $resource = $this->createResourceMock();
        $formatter = $this->createFormatterMock();
        $formatter->shouldReceive('toResponse')->with($resource)->twice()->andReturn('Irrelevant Content');

        $response = new Response();
        $response->setResource($resource);
        $this->assertSame($response->getContent(), '');

        $response->setFormatter($formatter);
        $this->assertSame($response->getContent(), 'Irrelevant Content');

        ob_start();
        $response->sendContent();
        $string = ob_get_clean();
        $this->assertContains('Irrelevant Content', $string);
    }

    public function testGetContentWithNoResourceShouldBeEmpty()
    {
        $response = new Response();

        $content = $response->getContent();

        $this->assertThat($content, $this->equalTo(''));
    }

    public function testContentTypeFrom()
    {
        $response = new Response();
        $response->setFormatter(new HALJsonFormatter);

        $this->assertEquals('application/hal+json', $response->headers->get('Content-Type'));
    }

    public function testContentTypeCharset()
    {
        $response = new Response();
        $response->setContentType('text/css');

        $this->assertEquals('text/css', $response->headers->get('Content-Type'));
    }

}
