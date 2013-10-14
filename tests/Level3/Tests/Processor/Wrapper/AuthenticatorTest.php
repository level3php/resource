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

    public function createWrapper()
    {
        $this->request = $this->createRequestMockSimple();

        $this->method = $this->makeAuthenticationMethodMock($this->request);
        return new Authenticator($this->method);
    }

    /**
     * @dataProvider provider
     */
    public function testAuthentication($method)
    {
        $request = $this->createResponseMock(); ;
        $execution = function($request) use ($request) { 
            return $request;
        };

        $wrapper = $this->createWrapper();
        $wrapper->$method($execution, $this->request);
    }

    public function provider()
    {
        return array(
            array('get'), 
            array('find'), 
            array('post'), 
            array('patch'), 
            array('put'), 
            array('delete'),
        );
    }

    public function testErrorAuthentication()
    {
        $request = $this->createResponseMock(); ;
        $execution = function($request) use ($request) { 
            return $request;
        };


        $request = $this->createRequestMockSimple();
        $method = m::mock('Level3\Processor\Wrapper\Authenticator\Method');
        $wrapper = new Authenticator($method);

        $this->assertInstanceOf(
            'Level3\Messages\Response', 
            $wrapper->error($execution, $request)
        );
    }

    private function makeAuthenticationMethodMock($request)
    {
        $mock = m::mock('Level3\Processor\Wrapper\Authenticator\Method');
        $mock->shouldReceive('authenticate')
            ->with($request)->once();

        $mock->shouldReceive('modifyResponse')
            ->with(m::type('Level3\Messages\Response'))->once();

        return $mock;
    }
}
