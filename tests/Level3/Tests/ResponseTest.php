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
use Level3\Response;
use Teapot\StatusCode;
use Hal\Resource;

class ResponseTest extends TestCase {
    public function testConstructor()
    {
        $response = new Response();
        $this->assertNull($response->getResource());
        $this->assertSame(StatusCode::OK, $response->getStatus());
    }

    public function testConstructorDefaults()
    {
        $resource = new Resource('/test');
        $response = new Response($resource, StatusCode::NOT_FOUND);
        $this->assertSame($resource, $response->getResource());
        $this->assertSame(StatusCode::NOT_FOUND, $response->getStatus());
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
        $response->setStatus(StatusCode::NOT_FOUND);
        $this->assertSame(StatusCode::NOT_FOUND, $response->getStatus()); 
    }

    public function testAddHeader()
    {
        $response = new Response();
        $response->addHeader('foo', 'bar');
        $this->assertSame(array('foo' => 'bar'), $response->getHeaders()); 
    }

    public function testSetFormatJSON()
    {
        $resource = new Resource('/test');
        $response = new Response($resource);
        $response->setFormat(Response::AS_JSON);
        $this->assertSame(Response::AS_JSON, $response->getFormat());

        $headers = $response->getHeaders();
        $this->assertSame('application/hal+json', $headers['Content-Type']);
        $this->assertSame('{', substr($response->getContent(), 0, 1));
    }

    public function testSetFormatXML()
    {
        $resource = new Resource('/test');
        $response = new Response($resource);
        $response->setFormat(Response::AS_XML);
        $this->assertSame(Response::AS_XML, $response->getFormat());

        $headers = $response->getHeaders();
        $this->assertSame('application/hal+xml', $headers['Content-Type']); 
        $this->assertSame('<?xml', substr($response->getContent(), 0, 5));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetFormatInvalid()
    {
        $response = new Response();
        $response->setFormat('foo');
    }
}