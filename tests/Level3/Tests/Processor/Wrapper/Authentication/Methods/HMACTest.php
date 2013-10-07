<?php

namespace Level3\Tests\Processor\Wrapper\Authentication;

use Level3\Processor\Wrapper\Authentication\Exceptions\InvalidCredentials;
use Level3\Processor\Wrapper\Authentication\Methods\HMAC;
use Level3\Processor\Wrapper\Authorization\Role;
use Level3\Tests\TestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class HMACTest extends TestCase
{
    const IRRELEVANT_SIGNATURE = 'X';
    const IRRELEVANT_RESPONSE = 'XX';

    private $credentialsRepositoryMock;
    private $responseFactoryMock;
    private $headers;
    private $request;
    private $authenticatedUser;

    public function setUp()
    {
        parent::setUp();
        $this->credentialsRepositoryMock = m::mock('Level3\Processor\Wrapper\Authentication\CredentialsRepository');
        $this->authenticatedUser = AuthenticatedCredentialsBuilder::anAuthenticatedUser()->build();

        $this->method = new HMAC($this->credentialsRepositoryMock);
    }

    private function getAuthorizationHeader($useUppercaseSignature)
    {
        $signature = $this->createSignatureForNullContent();
        if ($useUppercaseSignature) $signature = strtoupper($signature);
        return sprintf( 'Token %s:%s',
            AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY, $signature);
    }

    private function createAuthenticatedCredentials()
    {
        return AuthenticatedCredentialsBuilder::withIrrelevantFields()->build();
    }

    private function createSignatureForNullContent()
    {
        return hash_hmac(HMAC::HASH_ALGORITHM, null, AuthenticatedCredentialsBuilder::IRRELEVANT_SECRET_KEY);
    }

    public function testAuthenticateRequestShouldThrowMissingCredentials()
    {
        $this->setExpectedException(
            'Level3\Processor\Wrapper\Authentication\Exceptions\MissingCredentials'
        );

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')->with(HMAC::AUTHORIZATION_HEADER)->andReturn(null);
        $this->method->authenticateRequest($request);
    }

    public function testAuthenticateRequestShouldThrowInvalidCredentials()
    {
        $this->setExpectedException('Level3\Exceptions\Forbidden');

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')->with('Authorization')->andReturn('Foo');
        $this->method->authenticateRequest($request);
    }

    public function testAuthenticateRequest()
    {
        $this->doTestAuthenticateRequest(false);
    }

    public function testAuthenticateRequestWithUppercaseSignature()
    {
        $this->doTestAuthenticateRequest(true);
    }

    private function doTestAuthenticateRequest($useUppercaseSignature)
    {
        $header = $this->getAuthorizationHeader($useUppercaseSignature);
        $authenticatedCredentials = $this->createAuthenticatedCredentials();

        $request = $this->createRequestWithHeaderAndCredentials($header, $authenticatedCredentials);

        $this->credentialsRepositoryMock
            ->shouldReceive('findByApiKey')
            ->with(AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY)
            ->andReturn($authenticatedCredentials);

        $request = $this->method->authenticateRequest($request);
    }

    private function createRequestWithHeaderAndCredentials($header, $credentials)
    {
        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getHeader')->with('Authorization')->andReturn($header);

        $request->shouldReceive('setCredentials')->with($credentials)->once();
        $request->shouldReceive('getContent')->andReturn('');
        return $request;
    }
}
