<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\BasicIpFirewall;
use Level3\Messages\Response;
use Psr\Log\LogLevel;
use Teapot\StatusCode;
use Exception;

use Mockery as m;

class BasicIpFirewallTest extends TestCase
{
    const EXAMPLE_IP_A = '127.0.0.1';
    const EXAMPLE_IP_B = '178.32.79.60';
    const EXAMPLE_IP_MALFORMED = '178.32.7960';
    const EXAMPLE_CIDR = '178.32.79.60/30';

    public function createWrapper()
    {
        $wrapper = new BasicIpFirewall();

        return $wrapper;
    }

    public function testError()
    {
        $request = $this->createResponseMock(); ;
        $execution = function($request) use ($request) { 
            return $request;
        };

        $request = $this->createRequestMockSimple();
        $wrapper = new BasicIpFirewall();

        $this->assertInstanceOf(
            'Level3\Messages\Response', 
            $wrapper->error($execution, $request)
        );
    }

    /**
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testNotIsInWhitelist()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP_A);

        $wrapper->addIpToWhitelist(self::EXAMPLE_CIDR);

        $wrapper->get(function($request) {
            return new Response();
        }, $request);    
    }

    public function testIsInWhitelist()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP_B);

        $wrapper->addIpToWhitelist(self::EXAMPLE_CIDR);

        $expected = $wrapper->get(function($request) {
            return new Response();
        }, $request);    

        $this->assertInstanceOf('Level3\Messages\Response', $expected);
    }

    /**
     * @expectedException Level3\Exceptions\Forbidden
     */
    public function testIsInBlacklist()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP_B);

        $wrapper->addIpToBlacklist(self::EXAMPLE_CIDR);

        $expected = $wrapper->get(function($request) {
            return new Response();
        }, $request);    
    }

    public function testDefault()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP_B);

        $expected = $wrapper->get(function($request) {
            return new Response();
        }, $request);    

        $this->assertInstanceOf('Level3\Messages\Response', $expected);
    }


    public function testNotIsInBlacklist()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP_B);

        $wrapper->addIpToWhitelist(self::EXAMPLE_CIDR);

        $expected = $wrapper->get(function($request) {
            return new Response();
        }, $request);    

        $this->assertInstanceOf('Level3\Messages\Response', $expected);
    }

    public function testAddBlacklistCIDR()
    {
        $wrapper = $this->createWrapper();
        $wrapper->addIpToBlacklist(self::EXAMPLE_CIDR);

        $this->assertSame(array(
            '178.32.79.60', 
            '178.32.79.61',
            '178.32.79.62',
            '178.32.79.63'
            ), $wrapper->getBlacklist()
        );
    }

    public function testAddWhitelistIP()
    {
        $wrapper = $this->createWrapper();
        $wrapper->addIpToWhitelist(self::EXAMPLE_IP_B);

        $this->assertSame(array(
            '178.32.79.60', 
            ), $wrapper->getWhitelist()
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testAddWhitelistIPAndBlacklist()
    {
        $wrapper = $this->createWrapper();
        $wrapper->addIpToWhitelist(self::EXAMPLE_IP_B);
        $wrapper->addIpToBlacklist(self::EXAMPLE_IP_B);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testAddWhitelistIPMalformed()
    {
        $wrapper = $this->createWrapper();
        $wrapper->addIpToWhitelist(self::EXAMPLE_IP_MALFORMED);
    }
}
