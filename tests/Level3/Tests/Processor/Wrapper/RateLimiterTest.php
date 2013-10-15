<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\RateLimiter;
use Level3\Messages\Response;

use Mockery as m;

class RateLimiterTest extends TestCase
{
    const EXAMPLE_IP = '127.0.0.1';
    const EXAMPLE_KEY = '{level3-127.0.0.1}';

    private $wrapper;

    public function createWrapper()
    {
        $this->redisMock = m::mock('Redis');

        $wrapper = new RateLimiter($this->redisMock);

        return $wrapper;
    }

    protected function callGetInWrapperAndGetResponse($method, $wrapper, $request = null, $response = null)
    {
        if (!$request) $request = $this->createRequestMockSimple();
        if (!$response) $response = new Response();
        return $wrapper->$method(function($request) use ($response) {
            return $response;
        }, $request);
    }

    public function testHeaderLimit()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->twice()->andReturn(self::EXAMPLE_IP);

        $this->redisMock->shouldReceive('get')->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('incr')->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('ttl')->with(self::EXAMPLE_KEY);

        $response = $this->callGetInWrapperAndGetResponse('get', $wrapper, $request);
        $this->assertSame(1000, $response->getHeader(RateLimiter::HEADER_LIMIT));

        $wrapper->setLimit(10);
        $response = $this->callGetInWrapperAndGetResponse('get', $wrapper, $request);
        $this->assertSame(10, $response->getHeader(RateLimiter::HEADER_LIMIT));

    }

    public function testHeaderRemaining()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP);

        $this->redisMock->shouldReceive('get')
            ->once()->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('incr')
            ->once()->with(self::EXAMPLE_KEY)->andReturn(1);
        $this->redisMock->shouldReceive('expire')
            ->once()->with(self::EXAMPLE_KEY, 3600)->andReturn(1);

        $this->redisMock->shouldReceive('ttl')->with(self::EXAMPLE_KEY);

        $response = $this->callGetInWrapperAndGetResponse('get', $wrapper, $request);
        $this->assertSame(999, $response->getHeader(RateLimiter::HEADER_REMAINING));
    }

    public function testSetResetAfterSecs()
    {
        $wrapper = $this->createWrapper();
        $wrapper->setResetAfterSecs(10);

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP);

        $this->redisMock->shouldReceive('get')
            ->once()->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('incr')
            ->once()->with(self::EXAMPLE_KEY)->andReturn(1);
        $this->redisMock->shouldReceive('expire')
            ->once()->with(self::EXAMPLE_KEY, 10)->andReturn(1);

        $this->redisMock->shouldReceive('ttl')->with(self::EXAMPLE_KEY);

        $response = $this->callGetInWrapperAndGetResponse('get', $wrapper, $request);
    }

    public function testHeaderReset()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP);

        $this->redisMock->shouldReceive('get')->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('incr')->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('ttl')->with(self::EXAMPLE_KEY)->andReturn(10);

        $response = $this->callGetInWrapperAndGetResponse('get', $wrapper, $request);
        $this->assertSame(time() + 10, $response->getHeader(RateLimiter::HEADER_RESET));
    }

    /**
     * @expectedException Level3\Exceptions\TooManyRequest
     */
    public function testLimitReached()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP);

        $this->redisMock->shouldReceive('get')->with(self::EXAMPLE_KEY)->andReturn(2000);
        $this->redisMock->shouldReceive('incr')->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('ttl')->with(self::EXAMPLE_KEY)->andReturn(10);

        $response = $this->callGetInWrapperAndGetResponse('get', $wrapper, $request);
    }

    public function testLimitReachedOnError()
    {
        $wrapper = $this->createWrapper();

        $request = $this->createRequestMockSimple();
        $request->shouldReceive('getClientIp')->once()->andReturn(self::EXAMPLE_IP);

        $this->redisMock->shouldReceive('get')->with(self::EXAMPLE_KEY)->andReturn(2000);
        $this->redisMock->shouldReceive('incr')->with(self::EXAMPLE_KEY);
        $this->redisMock->shouldReceive('ttl')->with(self::EXAMPLE_KEY)->andReturn(10);

        $response = $this->callGetInWrapperAndGetResponse('error', $wrapper, $request);
    }
}
