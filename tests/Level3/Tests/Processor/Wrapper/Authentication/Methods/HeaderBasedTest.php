<?php

namespace Level3\Tests\Processor\Wrapper\Authentication;

use Level3\Tests\TestCase;
use Mockery as m;

use Level3\Processor\Wrapper\Authenticator\Methods\HeaderBased;
use Level3\Messages\Request;

class HeaderBasedTest extends TestCase
{
    /**
     * @expectedException Level3\Processor\Wrapper\Authenticator\Exceptions\MissingCredentials
     */
    public function testAuthenticateRequestWithOutHeader()
    {
        $method = new HeaderBasedMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(HeaderBased::AUTHORIZATION_HEADER)
            ->once()->andReturn(null);


        $response = $this->createResponseMock();
        $method->authenticate($request);
    }

    public function testAuthenticateRequestWithOutHeaderAllowed()
    {
        $method = new HeaderBasedMock();
        $method->continueWithoutAuthentication(true);

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(HeaderBased::AUTHORIZATION_HEADER)
            ->once()->andReturn(null);


        $response = $this->createResponseMock();
        $method->authenticate($request);
        $method->modifyResponse($response);
    }

    public function testAuthenticateRequest()
    {
        $method = new HeaderBasedMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(HeaderBased::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: Basic foo');

        $request->shouldReceive('mustBeCalledSetCredentials')
            ->once()->andReturn(null);

        $response = $this->createResponseMock();
        $method->authenticate($request);
    }

    /**
     * @expectedException Level3\Processor\Wrapper\Authenticator\Exceptions\InvalidScheme
     */
    public function testAuthenticateRequestInvalidScheme()
    {
        $method = new HeaderBasedMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(HeaderBased::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: Foo QWxhZGRpbjpvcGVuHNlc2FtZQ==');


        $response = $this->createResponseMock();
        $method->authenticate($request);
    }

    /**
     * @expectedException Level3\Processor\Wrapper\Authenticator\Exceptions\MalformedCredentials
     */
    public function testAuthenticateRequestInvalid()
    {
        $method = new HeaderBasedMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(HeaderBased::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: QWxhZGRpbjpvcGVuHNlc2FtZQ==');


        $response = $this->createResponseMock();
        $method->authenticate($request);
    }
}


class HeaderBasedMock extends HeaderBased
{
    protected $scheme = 'Basic';

    protected function verifyToken(Request $request, $token)
    {
        if ($token == 'foo') {
            return true;
        }

        return false;
    }

    protected function modifyRequest(Request $request)
    {
        $request->mustBeCalledSetCredentials();
    }
}