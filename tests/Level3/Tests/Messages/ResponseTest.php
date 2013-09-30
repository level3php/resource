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
use Level3\Formatter\JsonFormatter;
use Teapot\StatusCode;
use Mockery as m;

class ResponseTest extends TestCase
{    
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

        $this->assertThat($fooHeaders, $this->equalTo(array('bar', 'crap')));
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

        $this->assertThat($fooHeder, $this->equalTo(array('qux')));
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
        $response->setFormatter(new JsonFormatter);

        $this->assertEquals('application/hal+json', $response->headers->get('Content-Type'));
    }

    public function testContentTypeCharset()
    {
        $response = new Response();
        $response->setContentType('text/css');

        $this->assertEquals('text/css', $response->headers->get('Content-Type'));
    }

}