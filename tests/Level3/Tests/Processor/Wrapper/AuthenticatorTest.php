<?php
namespace Level3\Tests\Processor\Wrapper;

use Level3\Tests\TestCase;
use Level3\Processor\Wrapper\Authenticator;
use Level3\Processor\Wrapper\Authenticator\Method;
use Exception;
use Mockery as m;

class AuthenticatorTest extends TestCase
{
    private $wrapper;

    public function setUp()
    {
        parent::setUp();
        $this->request = $this->createRequestMockSimple();

        $this->method = $this->makeAuthenticationMethodMock($this->request);
        $this->wrapper = new Authenticator($this->method);
    }

    /**
     * @dataProvider provider
     */
    public function testAuthentication($method)
    {
        $execution = function($request) { 
            return true; 
        };

        $this->wrapper->$method($execution, $this->request);
    }

    public function provider()
    {
        return array(
            array('get'), 
            array('find'), 
            array('post'), 
            array('patch'), 
            array('put'), 
            array('delete')
        );
    }

    private function makeAuthenticationMethodMock($request)
    {
        $mock = m::mock('Level3\Processor\Wrapper\Authenticator\Method');
        $mock->shouldReceive('authenticateRequest')
            ->with($request)->once();

        return $mock;
    }
}
