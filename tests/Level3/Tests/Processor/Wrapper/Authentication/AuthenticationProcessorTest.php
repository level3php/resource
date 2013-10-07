<?php

namespace Level3\Tests\Processor\Wrapper\Authentication;

use Level3\Messages\RequestFactory;
use Level3\Processor\Wrapper\Authentication\AuthenticationProcessor;
use Teapot\StatusCode;
use Mockery as m;

class AuthenticationProcessorTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_SIGNATURE = 'X';
    const IRRELEVANT_RESPONSE = 'XX';

    private $requestProcessorMock;
    private $responseFactoryMock;
    private $authMethodMock;
    private $requestMock;

    private $authenticationProcessor;

    public function __construct($name = null, $data = array(), $dataName='') {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        $this->markTestSkipped(
            'The MySQLi extension is not available.'
        );
        
        $this->requestProcessorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->authMethodMock = m::mock('Level3\Processor\Wrapper\Authentication\AuthenticationMethod');
        $this->requestMock = m::mock('Level3\Messages\Request');

        $this->authenticationProcessor = new AuthenticationProcessor(
            $this->requestProcessorMock, $this->authMethodMock, $this->responseFactoryMock
        );
    }

    /**
     * @dataProvider methodsToAuthenticate
     */
    public function testMethod($methodName)
    {
        $this->shouldAuthenticateRequest();
        $this->requestProcessorMockShouldReceiveCallTo($methodName);

        $response = $this->authenticationProcessor->$methodName($this->requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methodsToAuthenticate
     */
    public function testFindWhenAuthenticateRequestThrowsMissingCredentials($methodName)
    {
        $this->methodAuthenticateRequestshouldThrowMissingCredentials();
        $this->requestProcessorMockShouldReceiveCallTo($methodName);

        $response = $this->authenticationProcessor->$methodName($this->requestMock);

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

    private function methodAuthenticateRequestShouldThrowMissingCredentials()
    {
        $this->authMethodMock
            ->shouldReceive('authenticateRequest')
            ->with($this->requestMock)->once()
            ->andThrow('Level3\Processor\Wrapper\Authentication\Exceptions\MissingCredentials');
    }

    private function requestProcessorMockShouldReceiveCallTo($method)
    {
        $this->requestProcessorMock
            ->shouldReceive($method)->once()
            ->with($this->requestMock)
            ->andReturn(self::IRRELEVANT_RESPONSE);
    }

    private function shouldAuthenticateRequest()
    {
        $this->authMethodMock->shouldReceive('authenticateRequest')->with($this->requestMock)->once()
            ->andReturn($this->requestMock);
    }
}
