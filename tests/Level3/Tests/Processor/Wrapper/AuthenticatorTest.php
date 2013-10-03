<?php
namespace Level3\Tests\Processor\Wrapper;

use Level3\Tests\TestCase;
use Level3\Processor\Wrapper\Authentication\Authenticator;
use Level3\Processor\Wrapper\Authentication\AuthenticationMethod;
use Exception;
use Mockery as m;

class AuthenticatorTest extends TestCase
{
    private $wrapper;

    public function setUp()
    {
        parent::setUp();
        $this->wrapper = new Authenticator();
    }

    /**
     * @dataProvider provider
     */
    public function testAuthentication($method)
    {
        $request = $this->createRequestMockSimple();
        $execution = function($request) { return true; };

        $authenticationMethod = $this->makeAuthenticationMethodMock($request);
        $this->wrapper->setAuthenticationMethod($authenticationMethod);

        $this->wrapper->$method($execution, $request);
    }

    public function provider()
    {
        return array(
            array('get'), array('find'), array('post'), array('patch'), array('put'), array('delete')
        );
    }

    private function makeAuthenticationMethodMock($request)
    {
        $mock = m::mock('Level3\Processor\Wrapper\Authentication\AuthenticationMethod');
        $mock->shouldReceive('authenticateRequest')
            ->with($request)->once();
        return $mock;
    }
}
