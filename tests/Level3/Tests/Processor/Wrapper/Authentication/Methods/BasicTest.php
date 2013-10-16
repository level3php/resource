<?php

namespace Level3\Tests\Processor\Wrapper\Authenticator;

use Level3\Processor\Wrapper\Authenticator\Methods\Basic;
use Level3\Tests\TestCase;
use Teapot\StatusCode;
use Mockery as m;

class BasicTest extends TestCase
{
    const VALID_TOKEN = 'Zm9vOmJhcg==';
    const INVALID_TOKEN = 'YmFyOmZvbw==';
    const MALFORMED_TOKEN = 'foux';
    CONST INVALID_ALGORITHM = 'foo';

    public function testAuthenticateRequest()
    {
        $method = new BasicMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(Basic::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: Basic '. self::VALID_TOKEN);

        $request->shouldReceive('setCredentials')
            ->once()
            ->with(m::on(function($credentials) {
                return $credentials->isAuthenticated();
            }));

        $method->authenticateRequest($request, 'get');
    }

    public function testSetRealm()
    {
        $method = new BasicMock();
        $method->setRealm('test');

        $response = $this->createResponseMock();
        $response->shouldReceive('getStatusCode')
            ->once()->andReturn(StatusCode::UNAUTHORIZED);

       $response->shouldReceive('setHeader')
            ->once()->with(Basic::WWW_AUTHENTICATE_HEADER, 'Basic realm="test"')
            ->andReturn(StatusCode::UNAUTHORIZED);

        $method->modifyResponse($response, 'get');
    }


    /**
     * @expectedException Level3\Processor\Wrapper\Authenticator\Exceptions\MalformedCredentials
     */
    public function testAuthenticateRequestMalformed()
    {
        $method = new BasicMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(Basic::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: Basic '. self::MALFORMED_TOKEN);

        $method->authenticateRequest($request, 'get');
    }

    /**
     * @expectedException Level3\Processor\Wrapper\Authenticator\Exceptions\Unauthorized
     */
    public function testAuthenticateRequestInvalid()
    {
        $method = new BasicMock();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')
            ->with(Basic::AUTHORIZATION_HEADER)
            ->twice()->andReturn('Authorization: Basic '. self::INVALID_TOKEN);

        $method->authenticateRequest($request, 'get');
    }
}

class BasicMock extends Basic
{
    protected function validateUserAndPassword($user, $password)
    {
        if ($user == 'foo' && $password == 'bar') {
            return true;
        }

        return false;
    }
}
