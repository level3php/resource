<?php

namespace Level3\Tests\Security\Authentication;

use Level3\Messages\RequestFactory;
use Level3\Security\Authentication\Exceptions\InvalidCredentials;
use Level3\Security\Authentication\Methods\HMAC;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class HMACTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_SIGNATURE = 'X';
    const IRRELEVANT_RESPONSE = 'XX';

    private $credentialsRespositoryMock;
    private $responseFactoryMock;
    private $requestFactory;
    private $headers;
    private $request;
    private $authenticatedUser;

    public function setUp()
    {
        $this->credentialsRespositoryMock = m::mock('Level3\Security\Authentication\CredentialsRepository');
        $this->requestFactory = new RequestFactory();
        $this->headers = $this->createHeaders();
        $this->authenticatedUser = AuthenticatedCredentialsBuilder::anAuthenticatedUser()->build();

        $this->method = new HMAC($this->credentialsRespositoryMock);
    }

    private function createHeaders()
    {
        return array(
            'Authorization' => sprintf(
                '%s%s%s%s%s',
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

    private function headerShouldBeMissing($headerName)
    {
        unset($this->headers[$headerName]);
        $this->initRequest();
    }

    private function initRequest()
    {
        $this->request = $this->requestFactory->clear()
            ->withSymfonyRequest(new Request())
            ->create();

        $this->request->headers->add($this->headers);
    }

    /**
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testAuthenticateRequestShouldThrowInvalidCredentials()
    {
        $this->extractAuthContentShouldThrowInvalidCredentials();

        $this->method->authenticateRequest($this->request);
    }

    private function extractAuthContentShouldThrowInvalidCredentials()
    {
        $this->initRequest();
        $this->request->headers->add(
            array('Authorization' =>
                sprintf(
                    '%s%s%s%s%s',
                    'XXX',
                    ' ',
                    AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY,
                    ':',
                    $this->createSignatureForNullContent()
                )
            )
        );
    }

    /**
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testAuthenticateRequestShouldThrowBadCredentials()
    {
        $this->extractAuthContentShouldThrowBadCredentials();

        $this->method->authenticateRequest($this->request);
    }

    private function extractAuthContentShouldThrowBadCredentials()
    {
        $this->initRequest();
        $this->credentialsRepositoryShouldHaveUser();
        $this->request->headers->add(
            array('Authorization' =>
            sprintf(
                '%s%s%s%s%s',
                'Token',
                ' ',
                AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY,
                ':',
                'asdfasd'
            )
            )
        );
    }

    public function testAuthenticateRequest()
    {
        $this->shouldAuthenticateRequest();

        $request = $this->method->authenticateRequest($this->request);

        $this->assertInstanceOf(
            'Level3\Security\Authentication\AuthenticatedCredentials',
            $request->getCredentials()
        );
    }

    public function testAuthenticateRequestWithUppercaseSignature()
    {
        $this->shouldAuthenticateRequest();

        $this->request->headers->add(
            array('Authorization' =>
            sprintf(
                '%s%s%s%s%s',
                'Token',
                ' ',
                AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY,
                ':',
                strtoupper($this->createSignatureForNullContent())
            )
            )
        );

        $request = $this->method->authenticateRequest($this->request);

        $this->assertInstanceOf(
            'Level3\Security\Authentication\AuthenticatedCredentials',
            $request->getCredentials()
        );
    }

    private function shouldAuthenticateRequest()
    {
        $this->initRequest();
        $this->credentialsRepositoryShouldHaveUser();
    }

    private function headerShouldBePresent($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
        $this->initRequest();
    }

    private function requestFactoryShouldCreateForbiddenResponse()
    {
        $this->responseFactoryMock->shouldReceive('createResponse')->once()->with(null, StatusCode::FORBIDDEN)
            ->andReturn(self::IRRELEVANT_RESPONSE);
    }

    private function credentialsRepositoryShouldHaveUser()
    {
        $this->credentialsRespositoryMock->shouldReceive('findByApiKey')->with(
            AuthenticatedCredentialsBuilder::IRRELEVANT_API_KEY
        )->once()
            ->andReturn($this->authenticatedUser);
    }
}
