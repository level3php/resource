<?php

namespace Level3\Tests\Security\Authorization;

use Level3\Resources\YamlConfigParser;
use Level3\Security\Authorization\AclAuthorizationProcessor;
use Level3\Tests\Security\Authentication\AuthenticatedCredentialsBuilder;
use Mockery as m;

class AclAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_RESPONSE = 'X';
    private $configParser;
    private $requestProcessorMock;
    private $responseFactoryMock;
    private $aclAuthorizationProcessor;

    public function setUp()
    {
        $this->configParser = new YamlConfigParser(__DIR__ . '/../../Resources/acl.yaml');
        $this->requestProcessorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
        $this->aclAuthorizationProcessor = new AclAuthorizationProcessor($this->requestProcessorMock, $this->configParser, $this->responseFactoryMock);
    }

    public function tearDown()
    {

    }

    /**
     * @dataProvider methods
     */
    public function testAuthorizeMethodsWithMatchingRequirements($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/bla/123456', '123456');
        $this->requestProcessorMock->shouldReceive($method)->with($requestMock)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->aclAuthorizationProcessor->$method($requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testAthorizeMethodsShouldFailWithNoSpecifiMethodsInConfig($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/with-no-specific-methods', '123456');
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array(), 403)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->aclAuthorizationProcessor->$method($requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testAthorizeMethodsShouldFailDueToInexistingRoute($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/b', '123456');
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array(), 403)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->aclAuthorizationProcessor->$method($requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testAthorizeMethodsShouldFailWithMissingAPIKEY($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/bla/123456', 'aaaa');
        $this->responseFactoryMock->shouldReceive('createFromDataAndStatusCode')->with(array(), 403)->once()
            ->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->aclAuthorizationProcessor->$method($requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     */
    public function testAuthorizeMethodsWithoutMatchingRequirements($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/no-reqs/aaa', '123456');
        $this->requestProcessorMock->shouldReceive($method)->with($requestMock)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->aclAuthorizationProcessor->$method($requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Security\Authorization\AclMatchingMethodNotFound
     */
    public function testAuthorizeMethodsWithInvalidMatching($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/invalid-matching/aaa', '123456');
        $this->requestProcessorMock->shouldReceive($method)->with($requestMock)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->aclAuthorizationProcessor->$method($requestMock);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function methods()
    {
        return array(
            array('find'),
            array('get'),
            array('put'),
            array('post'),
            array('delete')
        );
    }

    /**
     * @expectedException Level3\Resources\Exceptions\ConfigError
     */
    public function testConstructorShouldFail()
    {
        $configParser = new YamlConfigParser(__DIR__ . '/../../Resources/valid.yaml');
        new AclAuthorizationProcessor($this->requestProcessorMock, $configParser, $this->responseFactoryMock);
    }

    private function createRequestWithPathAndCredentialsAPIKey($path, $apiKey)
    {
        $requestMock = m::mock('Level3\Messages\Request');
        $credentials = AuthenticatedCredentialsBuilder::anAuthenticatedUser()->withApiKey($apiKey)->build();
        $requestMock->shouldReceive('getCredentials')->withNoArgs()->once()->andReturn($credentials);
        $requestMock->shouldReceive('getPathInfo')->with()->twice()->andReturn($path);
        return $requestMock;
    }
}