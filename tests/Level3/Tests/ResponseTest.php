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

use Level3\Hal\Resource;
use Level3\Messages\Response;
use Teapot\StatusCode;
use Mockery as m;

class ResponseTest extends TestCase
{
    public function testConstructor()
    {
        $response = new Response();
        $this->assertNull($response->getResource());
        $this->assertSame(StatusCode::OK, $response->getStatusCode());
    }

    public function testConstructorDefaults()
    {
        $resource = new Resource('/test');
        $response = new Response($resource, StatusCode::NOT_FOUND);
        $this->assertSame($resource, $response->getResource());
        $this->assertSame(StatusCode::NOT_FOUND, $response->getStatusCode());
    }

    public function testSetResource()
    {
        $resource = new Resource('/test');
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
        $response->setHeader('foo', 'crap');

        $fooHeder = $response->getHeaders('foo');

        $this->assertThat($fooHeder, $this->equalTo(array('crap')));
    }

    public function testSetHeaderTwiceAndGetHeader()
    {
        $response = new Response();
        $response->setHeader('foo', 'bar');
        $response->setHeader('foo', 'crap');

        $fooHeder = $response->getHeader('foo');

        $this->assertThat($fooHeder, $this->equalTo('crap'));
    }

    public function testGetContent()
    {
        $response = new Response();
        $resourceMock = m::mock('Level3\Hal\Resource');
        $resourceMock->shouldReceive('format')->withNoArgs()->once()->andReturn('Irrelevant Content');
        $response->setResource($resourceMock);

        $content = $response->getContent();

        $this->assertThat($content, $this->equalTo('Irrelevant Content'));
    }

    public function testGetContentWithNoResourceShouldBeEmpty()
    {
        $response = new Response();

        $content = $response->getContent();

        $this->assertThat($content, $this->equalTo(''));
    }
}