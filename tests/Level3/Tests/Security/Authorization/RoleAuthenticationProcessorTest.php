<?php

namespace Level3\Tests\Security\Authorization;

use Level3\Resources\YamlConfigParser;
use Level3\Security\Authentication\Credentials;
use Level3\Security\Authentication\AuthenticatedCredentials;
use Level3\Security\Authentication\AnonymousCredentials;
use Level3\Security\Authorization\Role;
use Level3\Security\Authorization\RoleAuthorizationProcessor;
use Level3\Tests\Security\Authentication\AuthenticatedCredentialsBuilder;
use Mockery as m;

class RoleAuthenticationProcessorTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_RESPONSE = 'X';

    private $configParser;
    private $requestProcessorMock;
    private $responseFactoryMock;
    private $roleAuthorizationProcessor;

    public function setUp()
    {
        $this->requestProcessorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->responseFactoryMock = m::mock('Level3\Messages\ResponseFactory');
    }

    /**
     * @dataProvider methods
     */
    public function testShouldAuthorizeRequest($methodName)
    {
        $request = $this->setupRequestWithMocksUsingFile(__DIR__ . '/../../Resources/role-all-methods.yaml');
        $this->requestProcessorMock->shouldReceive($methodName)->with($request)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->roleAuthorizationProcessor->$methodName($request);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    private function createCredentialsWithAdminRole()
    {
        $role = new Role();
        $role->addAdminAccess();
        return AuthenticatedCredentialsBuilder::anAuthenticatedUser()
            ->withRole($role)
            ->build();
    }

    /**
     * @expectedException Level3\Resources\Exceptions\ConfigError
     */
    public function testConstructorWithInvalidConfiguration()
    {
        $this->configParser = new YamlConfigParser(__DIR__.'/../../Resources/valid.yaml');
        $this->roleAuthorizationProcessor = new RoleAuthorizationProcessor($this->requestProcessorMock, $this->responseFactoryMock, $this->configParser);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldReturnForbiddenResponseDueToSetup($methodName)
    {
        $request = $this->setupRequestWithMocksUsingFile(__DIR__ . '/../../Resources/role-no-methods.yaml');

        $response = $this->roleAuthorizationProcessor->$methodName($request);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldReturnForbiddenResponseDueToNotMatchingRoute($methodName)
    {
        $request = $this->setupRequestWithMocksUsingFile(__DIR__ . '/../../Resources/role-not-matching-route.yaml');

        $response = $this->roleAuthorizationProcessor->$methodName($request);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldCreateForbiddenResponseDueToRoles($methodName)
    {
        $credentials = new AuthenticatedCredentials(1, 'x', 'xx', new Role(), '', '');
        $this->doTestForbiddenResponseDueToRolesForCredentials($methodName, $credentials);

    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldCreateForbiddenResponseDueToRolesWithAnonymousCredentials($methodName)
    {
        $credentials = new AnonymousCredentials();
        $this->doTestForbiddenResponseDueToRolesForCredentials($methodName, $credentials);
    }

    private function doTestForbiddenResponseDueToRolesForCredentials($methodName, Credentials $credentials)
    {
        $this->configParser = new YamlConfigParser(__DIR__.'/../../Resources/role-all-methods.yaml');
        $this->roleAuthorizationProcessor = new RoleAuthorizationProcessor(
            $this->requestProcessorMock, $this->responseFactoryMock, $this->configParser
        );
        $request = m::mock('Level3\Messages\Request');
        $request->shouldReceive('getPathInfo')->withNoArgs()->once()->andReturn('/bla/blah');
        $request->shouldReceive('getCredentials')->withNoArgs()->once()->andReturn($credentials);

        $response = $this->roleAuthorizationProcessor->$methodName($request);

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

    private function setupRequestWithMocksUsingFile($file)
    {
        $this->configParser = new YamlConfigParser($file);
        $this->roleAuthorizationProcessor = new RoleAuthorizationProcessor($this->requestProcessorMock, $this->responseFactoryMock, $this->configParser);
        $request = m::mock('Level3\Messages\Request');
        $request->shouldReceive('getPathInfo')->withNoArgs()->once()->andReturn('/bla/blah');
        $credentials = $this->createCredentialsWithAdminRole();
        $request->shouldReceive('getCredentials')->withNoArgs()->once()->andReturn($credentials);
        return $request;
    }
}
