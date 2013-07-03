<?php

namespace Level3\Tests\Security\Authentication;

use Level3\Messages\RequestFactory;
use Level3\Security\Authentication\Methods\HMAC;
use Mockery as m;

class HMACTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_SIGNATURE = 'X';
    const IRRELEVANT_RESPONSE = 'XX';

    private $requestProcessorMock;
    private $userRepositoryMock;
    private $responseFactoryMock;
    private $requestFactory;
    private $headers;
    private $request;
    private $authenticatedUser;

    private $authenticationProcessor;

    public function setUp()
    {
        $this->userRepositoryMock = m::mock('Level3\Security\Authentication\UserRepository');
        $this->requestFactory = new RequestFactory();
        $this->headers = $this->createHeaders();
        $this->authenticatedUser = AuthenticatedCredentialsBuilder::anAuthenticatedUser()->build();

        $this->method = new HMAC($this->userRepositoryMock);
    }

    private function createHeaders()
    {
        return array(
            'Authorization' => sprintf('%s%s%s%s%s',
                'Token',
                ' ',
                AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY,
                ':',
                $this->createSignatureForNullContent()
            )
        );
    }

    private function createSignatureForNullContent()
    {
        return hash_hmac(HMAC::HASH_ALGORITHM, null, AuthenticatedCredentialsBuilder::IRRELEVANT_SECRET_KEY);
    }

    /**
     * @expectedException Level3\Security\Authentication\Exceptions\MissingCredentials
     */
    public function testAuthenticateRequestShouldThrowMissingCredentials()
    {
        $this->headerShouldBeMissing(HMAC::AUTHORIZATION_HEADER);
        $request = $this->method->authenticateRequest($this->request);
    }

    /**
     * @expectedException Level3\Security\Authentication\Exceptions\BadCredentials
     */
    public function testAuthenticateRequestShouldThrowBadCredentials()
    {
        $this->authenticateRequestShouldThrowBadCredentials();
        $request = $this->method->authenticateRequest($this->request);
    }
    

    public function testAuthenticateRequest()
    {
        $this->shouldAuthenticateRequest();

        $request = $this->method->authenticateRequest($this->request);
        $this->assertInstanceOf(
            'Level3\Security\Authentication\AuthenticatedUser',
            $request->getUser()
        );
    }

    private function headerShouldBeMissing($headerName)
    {
        unset($this->headers[$headerName]);
        $this->initRequest();
    }

    private function headerShouldBePresent($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
        $this->initRequest();
    }

    private function shouldAuthenticateRequest()
    {
        $this->initRequest();
        $this->userRepositoryMock->shouldReceive('findByApiKey')->with(AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY)->once()
            ->andReturn($this->authenticatedUser);
    }

    private function initRequest(){
        $this->request = $this->requestFactory->clear()
            ->withHeaders($this->headers)
            ->create();
    }

    private function authenticateRequestShouldThrowBadCredentials()
    {
        $this->initRequest();
        $this->userRepositoryMock->shouldReceive('findByApiKey')->with(AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY)->once()
            ->andThrow('Level3\Security\Authentication\Exceptions\BadCredentials');
    }

    private function requestFactoryShouldCreateForbiddenResponse()
    {
        $this->responseFactoryMock->shouldReceive('createResponse')->once()->with(null, StatusCode::FORBIDDEN)
            ->andReturn(self::IRRELEVANT_RESPONSE);
    }
}
