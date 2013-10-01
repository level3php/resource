<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Messages\Response;

use Level3\Processor\Wrapper;
use Level3\Exceptions\HTTPException;

use RuntimeException;
use Closure;
use Teapot\StatusCode;

class CrossOriginResourceSharing extends Wrapper
{
    const ALLOW_ORIGIN_WILDCARD = '*';
    const ALLOW_ORIGIN_HEADER = 'Access-Control-Allow-Origin';

    protected $allowOrigin;
    protected $exposeHeaders;
    protected $maxAge;
    protected $allowCredentials;
    protected $allowMethods;
    protected $allowHeaders;

    public function setAllowOrigin($allowOrigin)
    {
        if (
            $allowOrigin && 
            $allowOrigin != self::ALLOW_ORIGIN_WILDCARD && 
            !$this->isValidHost($host)
        ) {
            throw new RuntimeException('Malformed allow-origin, must be a host, null or *'); 
        }

        $this->allowOrigin = $allowOrigin;
    }

    public function setMultipleAllowOrigin(Array $allowOrigins)
    {
        foreach ($allowOrigins as $host) {
            if (!$this->isValidHost($host)) {
                throw new RuntimeException('Malformed allow-origin, must be a host'); 
            }
        }

        $this->allowOrigin = $allowOrigins;
    }

    protected function isValidHost($host)
    {
        return (boolean) parse_url($host);
    }

    public function getAllowOrigin()
    {
        return $this->allowOrigin;
    }

    public function setExposeHeaders(Array $exposeHeaders)
    {
        $this->exposeHeaders = $exposeHeaders;
    }

    public function getExposeHeaders()
    {
        return $this->exposeHeaders;
    }

    public function setMaxAge($maxAge)
    {
        if ($maxAge && (int) $maxAge > 0) {
            throw new RuntimeException('Malformed max-age, must be a number or null'); 
        }

        $this->maxAge = $maxAge;
    }

    public function getMaxAge()
    {
        return $this->maxAge;
    }

    public function setAllowCredentials($allowCredentials)
    {
        if (
            $allowCredentials !== null && 
            $allowCredentials !== true && 
            $allowCredentials !== false
        ) {
            throw new RuntimeException('Malformed allow-credentials, must be a boolean or null'); 
        }

        $this->allowCredentials = $allowCredentials;
    }

    public function getAllowCredentials()
    {
        return $this->allowCredentials;
    }

    public function setAllowMethods(Array $allowMethods = null)
    {
        $this->allowMethods = $allowMethods;
    }

    public function getAllowMethods()
    {
        return $this->allowMethods;
    }

    public function setAllowHeaders(Array $allowHeaders = null)
    {
        $this->allowHeaders = $allowHeaders;
    }

    public function getAllowHeaders()
    {
        return $this->allowHeaders;
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $response = $execution($request);
        $this->applyHeaders($response);

        return $response;
    }

    protected function applyHeaders(Response $response)
    {
        $this->applyAllowOriginHeader($response);
    }

    protected function applyAllowOriginHeader(Response $response)
    {
        if ($this->allowOrigin === null) {
            return;
        }

        $origins = $this->allowOrigin;
        if (!is_array($origins)) {
            $origins = array($origins);
        }

        foreach ($origins as $origin) {
            $response->addHeader(self::ALLOW_ORIGIN_HEADER, $origin);
        }
    }
    
}