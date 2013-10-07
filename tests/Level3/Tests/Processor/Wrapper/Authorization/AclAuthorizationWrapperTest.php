<?php

namespace Level3\Tests\Processor\Wrapper\Authorization;

use Level3\Processor\Wrapper\Authorization\AclAuthorizationWrapper;
use Level3\Resources\YamlConfigParser;
use Level3\Tests\Processor\Wrapper\Authentication\AuthenticatedCredentialsBuilder;
use Level3\Tests\TestCase;
use Mockery as m;
use stdClass;

class AclAuthorizationWrapperTest extends TestCase
{
    const IRRELEVANT_RESPONSE = 'X';
    private $configParser;
    private $wrapper;

    public function setUp()
    {
        parent::setUp();
        $this->configParser = new YamlConfigParser(TESTS_DIR . '/Level3/Tests/Resources/acl.yaml');
        $this->wrapper =  new AclAuthorizationWrapper($this->configParser);
    }

    /**
     * @dataProvider methods
     */
    public function testAuthorizeMethodsWithMatchingRequirements($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/bla/123456', '123456');
        $this->doTestCorrectResponseForRequest($method, $requestMock);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testAuthorizeMethodsShouldFailDueToInexistingRoute($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/b', '123456');
        $this->wrapper->$method($this->createDummyExecution(), $requestMock);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testAuthorizeMethodsShouldFailWithMissingAPIKEY($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/bla/123456', 'aaaa');
        $this->wrapper->$method($this->createDummyExecution(), $requestMock);
    }

    /**
     * @dataProvider methods
     */
    public function testAuthorizeMethodsWithoutMatchingRequirements($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/no-reqs/aaa', '123456');
        $this->doTestCorrectResponseForRequest($method, $requestMock);
    }

    private function doTestCorrectResponseForRequest($method, $request)
    {
        $expectedResponse = new stdClass();
        $execution = $this->createDummyExecution($expectedResponse);
        $response = $this->wrapper->$method($execution, $request);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Processor\Wrapper\Authorization\AclMatchingMethodNotFound
     */
    public function testAuthorizeMethodsWithInvalidMatching($method)
    {
        $requestMock = $this->createRequestWithPathAndCredentialsAPIKey('/invalid-matching/aaa', '123456');
        $this->wrapper->$method($this->createDummyExecution(), $requestMock);
    }

    /**
     * @expectedException Level3\Resources\Exceptions\ConfigError
     */
    public function testConstructorShouldFail()
    {
        $configParser = new YamlConfigParser(TESTS_DIR . '/Level3/Tests/Resources/valid.yaml');
        new AclAuthorizationWrapper($configParser);
    }


    private function createDummyExecution($retValue = true)
    {
        return function($request) use ($retValue) { return $retValue; };
    }

    private function createRequestWithPathAndCredentialsAPIKey($path, $apiKey)
    {
        $requestMock = m::mock('Level3\Messages\Request');
        $credentials = AuthenticatedCredentialsBuilder::anAuthenticatedUser()->withApiKey($apiKey)->build();
        $requestMock->shouldReceive('getCredentials')->withNoArgs()->once()->andReturn($credentials);
        $requestMock->shouldReceive('getPathInfo')->with()->andReturn($path);
        return $requestMock;
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

}
