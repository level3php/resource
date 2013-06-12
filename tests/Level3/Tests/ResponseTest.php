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

class ResponseTest extends TestCase {
    public function testConstructor()
    {
        $response = new Response();
        $this->assertNull($response->getHal());
        $this->assertSame(StatusCode::OK, $response->getStatus());
    }

    public function testConstructorDefaults()
    {
        $response = new Response(null, StatusCode::NOT_FOUND);
        $this->assertNull($response->getHal());
        $this->assertSame(StatusCode::NOT_FOUND, $response->getStatus());
    }

    public function testSetHal()
    {

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
        $response = new Response();
        $response->setFormat(Response::AS_JSON);
        $this->assertSame(Response::AS_JSON, $response->getFormat());

        $headers = $response->getHeaders();
        $this->assertSame('application/hal+json', $headers['Content-Type']); 
    }

    public function testSetFormatXML()
    {
        $response = new Response();
        $response->setFormat(Response::AS_XML);
        $this->assertSame(Response::AS_XML, $response->getFormat());

        $headers = $response->getHeaders();
        $this->assertSame('application/hal+xml', $headers['Content-Type']); 
    }
}