<?php

namespace Level3\Tests\Processor\Wrapper\Authentication;

use Level3\Tests\TestCase;
use Level3\Processor\Wrapper\Authenticator\Methods\HeaderBased;
use Level3\Messages\Request;
use Teapot\StatusCode;

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
        $method->authenticateRequest($request, 'get');
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
        $response->shouldReceive('getStatusCode')
            ->once()->andReturn(StatusCode::UNAUTHORIZED);

        $response->shouldReceive('setHeader')
            ->once()->with(HeaderBasedMock::WWW_AUTHENTICATE_HEADER, 'Basic')
            ->andReturn(StatusCode::UNAUTHORIZED);

        $method->authenticateRequest($request, 'get');
        $method->modifyResponse($response, 'get');
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
        $method->authenticateRequest($request, 'get');
    }

    /**
     * @expectedException Level3\Processor\Wrapper\Authenticator\Exceptions\Unauthorized
     */
    public function testAuthenticateRequestAndFaild()
    {
        $method = new HeaderBasedMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(HeaderBased::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: Basic bar');

        $response = $this->createResponseMock();
        $method->authenticateRequest($request, 'get');
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
        $method->authenticateRequest($request, 'get');
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
        $method->authenticateRequest($request, 'get');
    }

    public function testErrorNonUnauthorized()
    {
        $method = new HeaderBasedMock();

        $response = $this->createResponseMock();
        $response->shouldReceive('getStatusCode')
            ->once()->andReturn(StatusCode::FORBIDDEN);

        $method->modifyResponse($response, 'get');
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

    protected function modifyRequest(Request $request, $httpMethod)
    {
        $request->mustBeCalledSetCredentials();
    }
}
