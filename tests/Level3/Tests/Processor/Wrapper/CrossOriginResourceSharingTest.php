<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\CrossOriginResourceSharing as CORS;
use Level3\Messages\Response;
use Psr\Log\LogLevel;
use Teapot\StatusCode;
use Exception;

use Mockery as m;

class CrossOriginResourceSharingTest extends TestCase
{
    private $wrapper;

    public function createWrapper()
    {
        return new CORS();
    }

    protected function createRequestMockWithGetHeader($header, $value)
    {
        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with($header)->once()->andReturn($value);

        return $request;
    }

    protected function callGetInWrapperAndGetResponse($wrapper, $request = null)
    {
        if (!$request) $request = $this->createRequestMockSimple();
        
        return $wrapper->get(function($request) {
            return new Response();
        }, $request); 
    }

    public function testSetAllowOriginWildcard()
    {
        $url = 'http://foo.bar';
        $wrapper = $this->createWrapper();
        $wrapper->setAllowOrigin(CORS::ALLOW_ORIGIN_WILDCARD);

        $this->assertSame(CORS::ALLOW_ORIGIN_WILDCARD, $wrapper->getAllowOrigin());

        $request = $this->createRequestMockWithGetHeader(CORS::ORIGIN_HEADER, $url);
        $response = $this->callGetInWrapperAndGetResponse($wrapper, $request);
        $this->assertSame(CORS::ALLOW_ORIGIN_WILDCARD, $response->getHeader(CORS::ALLOW_ORIGIN_HEADER));
    }

    public function testSetAllowOriginHost()
    {
        $url = 'http://foo.bar';
        $wrapper = $this->createWrapper();
        $wrapper->setAllowOrigin($url);

        $this->assertSame($url, $wrapper->getAllowOrigin());

        $request = $this->createRequestMockWithGetHeader(CORS::ORIGIN_HEADER, $url);
        $response = $this->callGetInWrapperAndGetResponse($wrapper, $request);
        $this->assertSame($url, $response->getHeader(CORS::ALLOW_ORIGIN_HEADER));
    }

    /**
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testReadOriginInvalid()
    {
        $url = 'http://foo.bar';
        $requestUrl = 'http://baz.qux';

        $wrapper = $this->createWrapper();
        $wrapper->setAllowOrigin($url);

        $request = $this->createRequestMockWithGetHeader(CORS::ORIGIN_HEADER, $requestUrl);
        $response = $this->callGetInWrapperAndGetResponse($wrapper, $request);
    }

    /**
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testReadOriginNone()
    {
        $allowOrigin = '*';

        $wrapper = $this->createWrapper();
        $wrapper->setAllowOrigin($allowOrigin);

        $request = $this->createRequestMockWithGetHeader(CORS::ORIGIN_HEADER, null);
        $response = $this->callGetInWrapperAndGetResponse($wrapper, $request);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSetAllowOriginNotValidHost()
    {
        $url = 'foobar';
        $wrapper = $this->createWrapper();
        $wrapper->setAllowOrigin($url);
    }

    public function testSetMultipleAllowOrigin()
    {
        $urls = array(
            'http://foo.bar',
            'http://qux.baz'
        );

        $wrapper = $this->createWrapper();
        $wrapper->setMultipleAllowOrigin($urls);

        $this->assertSame($urls, $wrapper->getAllowOrigin());

        $request = $this->createRequestMockWithGetHeader(CORS::ORIGIN_HEADER, $urls[0]);
        $response = $this->callGetInWrapperAndGetResponse($wrapper, $request);
        $this->assertSame($urls, $response->getHeaders(CORS::ALLOW_ORIGIN_HEADER));
    }

    public function testSetExposeHeaders()
    {
        $headers = array('bar', 'foo');

        $wrapper = $this->createWrapper();
        $wrapper->setExposeHeaders($headers);

        $this->assertSame($headers, $wrapper->getExposeHeaders());

        $response = $this->callGetInWrapperAndGetResponse($wrapper);
        $this->assertSame('bar, foo', $response->getHeader(CORS::EXPOSE_HEADERS_HEADER));
    }

    public function testSetMaxAge()
    {
        $maxAge = 100;

        $wrapper = $this->createWrapper();
        $wrapper->setMaxAge($maxAge);

        $this->assertSame($maxAge, $wrapper->getMaxAge());

        $response = $this->callGetInWrapperAndGetResponse($wrapper);
        $this->assertSame($maxAge, $response->getHeader(CORS::MAX_AGE_HEADER));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSetMaxAgeNotANumber()
    {
        $invalidAge = 'foobar';
        $wrapper = $this->createWrapper();
        $wrapper->setMaxAge($invalidAge);
    }

    public function testSetAllowCredentialsTrue()
    {
        $allow = true;

        $wrapper = $this->createWrapper();
        $wrapper->setAllowCredentials($allow);

        $this->assertSame($allow, $wrapper->getAllowCredentials());

        $response = $this->callGetInWrapperAndGetResponse($wrapper);
        $this->assertSame('true', $response->getHeader(CORS::ALLOW_CRENDENTIALS_HEADER));
    }

    public function testSetAllowCredentialsFalse()
    {
        $allow = false;

        $wrapper = $this->createWrapper();
        $wrapper->setAllowCredentials($allow);

        $this->assertSame($allow, $wrapper->getAllowCredentials());

        $response = $this->callGetInWrapperAndGetResponse($wrapper);
        $this->assertSame('false', $response->getHeader(CORS::ALLOW_CRENDENTIALS_HEADER));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSetAllowCredentialsNonBoolean()
    {
        $invalidAllow = 'foobar';
        $wrapper = $this->createWrapper();
        $wrapper->setAllowCredentials($invalidAllow);
    }

    public function testSetAllowMethods()
    {
        $methods = array('bar', 'foo');

        $wrapper = $this->createWrapper();
        $wrapper->setAllowMethods($methods);

        $this->assertSame($methods, $wrapper->getAllowMethods());

        $response = $this->callGetInWrapperAndGetResponse($wrapper);
        $this->assertSame('bar, foo', $response->getHeader(CORS::ALLOW_METHODS_HEADER));
    }

    public function testSetAllowHeaders()
    {
        $headers = array('bar', 'foo');

        $wrapper = $this->createWrapper();
        $wrapper->setAllowHeaders($headers);

        $this->assertSame($headers, $wrapper->getAllowHeaders());

        $response = $this->callGetInWrapperAndGetResponse($wrapper);
        $this->assertSame('bar, foo', $response->getHeader(CORS::ALLOW_HEADERS_HEADER));
    }
}
