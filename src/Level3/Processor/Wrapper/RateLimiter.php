<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Messages\Response;
use Level3\Processor\Wrapper;
use Level3\Exceptions\TooManyRequest;

use Redis;
use Closure;

class RateLimiter extends Wrapper
{
    const KEY_PATTERN = '{level3-%s}';
    const HEADER_LIMIT = 'X-RateLimit-Limit';
    const HEADER_REMAINING = 'X-RateLimit-Remaining';
    const HEADER_RESET = 'X-RateLimit-Reset';

    protected $redis;
    protected $limit = 1000;
    protected $resetAfterSecs = 3600;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setResetAfterSecs($secs)
    {
        $this->resetAfterSecs = $secs;
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $key = $this->getKey($request);

        if ($this->isLimitReached($key)) {
            if ($method != 'error') {
                throw new TooManyRequest();
            }

            $remaining = 0;
        } else {
            $remaining = $this->limit - $this->increaseAndReturnCount($key);
        }

        $response = $execution($request);

        $reset = $this->getResetDate($key);
        $this->addHeadersToResponse($response, $remaining, $reset);

        return $response;
    }

    protected function addHeadersToResponse(Response $response, $remaining, $reset)
    {
        $response->addHeader(self::HEADER_LIMIT, $this->limit);
        $response->addHeader(self::HEADER_REMAINING, $remaining);
        $response->addHeader(self::HEADER_RESET, $reset);
    }

    protected function getKey(Request $request)
    {
        return sprintf(self::KEY_PATTERN, $request->getClientIp());
    }

    protected function isLimitReached($key)
    {
        $current = $this->redis->get($key);
        if ((int) $current > $this->limit) {
            return true;
        }

        return false;
    }

    protected function increaseAndReturnCount($key)
    {
        $current = $this->redis->incr($key);
        if ($current == 1) {
            $this->redis->expire($key, $this->resetAfterSecs);
        }

        return $current;
    }

    protected function getResetDate($key)
    {
        return time() + $this->redis->ttl($key);
    }
}
