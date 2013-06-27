<?php

namespace Level3\Tests\Security\Authentication;

use Level3\Messages\RequestFactory;
use Level3\Security\Authentication\AuthenticationProcessor;
use Teapot\StatusCode;
use Mockery as m;

class AuthenticationProcessorTest extends \PHPUnit_Framework_TestCase
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

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->requestProcessorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->userRepositoryMock = m::mock('Level3\Security\Authentication\UserRepository');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->requestFactory = new RequestFactory();
        $this->headers = $this->createHeaders();
        $this->authenticatedUser = AuthenticatedUserBuilder::anAuthenticatedUser()->build();

        $this->authenticationProcessor = new AuthenticationProcessor(
            $this->requestProcessorMock, $this->userRepositoryMock, $this->responseFactoryMock
        );
    }

    private function createHeaders()
    {
        return array(
            'Authorization' => sprintf('%s%s%s%s%s',
                'Token',
                ' ',
                AuthenticatedUserBuilder::IRRELEVANT_API_KEY,
                ':',
                $this->createSignatureForNullContent()
            )
        );
    }

    private function createSignatureForNullContent()
    {
        return hash_hmac(AuthenticationProcessor::HASH_ALGORITHM, null, AuthenticatedUserBuilder::IRRELEVANT_SECRET_KEY);
    }

    /**
     *
     */
    public function testAuthenticateRequestShouldThrowMissingApiKey()
    {
        return;
        $this->headerShouldBeMissing(AuthenticationProcessor::AUTHORIZATION_HEADER);

        $method = $this->getAccessibleMethod('authenticateRequest');
        $method->invokeArgs($this->authenticationProcessor, array($this->request));
    }

    /**
     *
     */
    public function testAuthenticateRequestShouldThrowUserNotFound()
    {
        return;
        $this->authenticateRequestShouldThrowUserNotFound();

        $method = $this->getAccessibleMethod('authenticateRequest');
        $method->invokeArgs($this->authenticationProcessor, array($this->request));
    }

    public function testAuthenticateRequest()
    {
        return;
        $this->shouldAuthenticateRequest();

        $method = $this->getAccessibleMethod('authenticateRequest');
        $request = $method->invokeArgs($this->authenticationProcessor, array($this->request));

        $this->assertThat($request->getUser(), $this->equalTo($this->authenticatedUser));
    }

    private function getAccessibleMethod($methodName)
    {
        $class = new \ReflectionClass('Level3\Security\Authentication\AuthenticationProcessor');
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @dataProvider methodsToAuthenticate
     */
    public function testFindWhenAuthenticateRequestThrowsUserNotFound($methodName)
    {
        $this->authenticateRequestShouldThrowUserNotFound();
        $this->requestFactoryShouldCreateForbiddenResponse();

        $response = $this->authenticationProcessor->$methodName($this->request);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methodsToAuthenticate
     */
    public function testFindWhenAuthenticateRequestThrowsMissingApiKey($methodName)
    {
        return;
        $this->headerShouldBeMissing(AuthenticationProcessor::AUTHORIZATION_HEADER);
        $this->requestProcessorMock->shouldreceive($methodName)->with($this->request)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->authenticationProcessor->$methodName($this->request);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methodsToAuthenticate
     */
    public function testFindWhenAuthenticateRequestThrowsInvalidSignature($methodName)
    {
        $this->shouldAuthenticateRequest();
        $this->headerShouldBePresent('Authorization', sprintf('%s%s%s%s%s',
            'Token',
            ' ',
            AuthenticatedUserBuilder::IRRELEVANT_API_KEY,
            ':',
            ''
        ));
        $this->requestFactoryShouldCreateForbiddenResponse();

        $response = $this->authenticationProcessor->$methodName($this->request);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function methodsToAuthenticate()
    {
        return array(
            array('find'),
            array('get'),
            array('post'),
            array('put'),
            array('delete')
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
        $this->userRepositoryMock->shouldReceive('findByApiKey')->with(AuthenticatedUserBuilder::IRRELEVANT_API_KEY)->once()
            ->andReturn($this->authenticatedUser);
    }

    private function initRequest(){
        $this->request = $this->requestFactory->clear()
            ->withHeaders($this->headers)
            ->create();
    }

    private function authenticateRequestShouldThrowUserNotFound()
    {
        $this->initRequest();
        $this->userRepositoryMock->shouldReceive('findByApiKey')->with(AuthenticatedUserBuilder::IRRELEVANT_API_KEY)->once()
            ->andThrow('Level3\Security\Authentication\Exceptions\UserNotFound');
    }

    private function requestFactoryShouldCreateForbiddenResponse()
    {
        $this->responseFactoryMock->shouldReceive('createResponse')->once()->with(null, StatusCode::FORBIDDEN)
            ->andReturn(self::IRRELEVANT_RESPONSE);
    }
}
