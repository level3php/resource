<?php

namespace Level3\Tests\Security\Authorization;

use Level3\Processor\Wrapper\Authentication\AnonymousCredentials;
use Level3\Processor\Wrapper\Authentication\AuthenticatedCredentials;
use Level3\Processor\Wrapper\Authentication\Credentials;
use Level3\Processor\Wrapper\Authorization\Role;
use Level3\Processor\Wrapper\Authorization\RoleAuthorizationWrapper;
use Level3\Resources\YamlConfigParser;
use Level3\Tests\Processor\Wrapper\Authentication\AuthenticatedCredentialsBuilder;
use Level3\Tests\TestCase;
use Mockery as m;
use stdClass;

class RoleAuthenticationProcessorTest extends TestCase
{
    const IRRELEVANT_RESPONSE = 'X';

    private $configParser;
    private $wrapper;

    /**
     * @dataProvider methods
     */
    public function testShouldAuthorizeRequest($method)
    {
        $request = $this->setupRequestWithMocksUsingFile(
            TESTS_DIR . '/Level3/Tests/Resources/role-all-methods.yaml');
        $expectedResponse = new stdClass();
        $execution = $this->createDummyExecution($expectedResponse);
        $response = $this->wrapper->$method($execution, $request);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @expectedException Level3\Resources\Exceptions\ConfigError
     */
    public function testConstructorWithInvalidConfiguration()
    {
        $this->configParser = new YamlConfigParser(TESTS_DIR . '/Level3/Tests/Resources/valid.yaml');
        new RoleAuthorizationWrapper($this->configParser);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldReturnForbiddenResponseDueToSetup($method)
    {
        $request = $this->setupRequestWithMocksUsingFile(
            TESTS_DIR . '/Level3/Tests/Resources/role-no-methods.yaml');

        $response = $this->wrapper->$method($this->createDummyExecution(), $request);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldReturnForbiddenResponseDueToNotMatchingRoute($method)
    {
        $request = $this->setupRequestWithMocksUsingFile(
            TESTS_DIR . '/Level3/Tests/Resources/role-not-matching-route.yaml');

        $response = $this->wrapper->$method($this->createDummyExecution(), $request);
    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldCreateForbiddenResponseDueToRoles($method)
    {
        $credentials = new AuthenticatedCredentials(1, 'x', 'xx', new Role(), '', '');
        $this->doTestForbiddenResponseDueToRolesForCredentials($method, $credentials);

    }

    /**
     * @dataProvider methods
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testShouldCreateForbiddenResponseDueToRolesWithAnonymousCredentials($method)
    {
        $credentials = new AnonymousCredentials();
        $this->doTestForbiddenResponseDueToRolesForCredentials($method, $credentials);
    }

    private function doTestForbiddenResponseDueToRolesForCredentials($method, Credentials $credentials)
    {
        $this->configParser = new YamlConfigParser(
            TESTS_DIR . '/Level3/Tests/Resources/role-all-methods.yaml');

        $this->wrapper = new RoleAuthorizationWrapper($this->configParser);
        $request = m::mock('Level3\Messages\Request');
        $request->shouldReceive('getPathInfo')->withNoArgs()->andReturn('/bla/blah');
        $request->shouldReceive('getCredentials')->withNoArgs()->once()->andReturn($credentials);

        $expectedResponse = new stdClass();
        $execution = $this->createDummyExecution($expectedResponse);
        $response = $this->wrapper->$method($execution, $request);

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
        $this->wrapper = new RoleAuthorizationWrapper($this->configParser);
        $request = m::mock('Level3\Messages\Request');
        $request->shouldReceive('getPathInfo')->withNoArgs()->once()->andReturn('/bla/blah');
        $credentials = $this->createCredentialsWithAdminRole();
        $request->shouldReceive('getCredentials')->withNoArgs()->once()->andReturn($credentials);
        return $request;
    }

    private function createCredentialsWithAdminRole()
    {
        $role = new Role();
        $role->addAdminAccess();
        return AuthenticatedCredentialsBuilder::anAuthenticatedUser()
            ->withRole($role)
            ->build();
    }

    private function createDummyExecution($retValue = true)
    {
        return function($request) use ($retValue) { return $retValue; };
    }
}
