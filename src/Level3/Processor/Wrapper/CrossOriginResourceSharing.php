<?php

namespace Level3\Processor\Wrapper;

use Level3\Messages\Request;
use Level3\Messages\Response;

use Level3\Processor\Wrapper;
use Level3\Exceptions\Forbidden;

use RuntimeException;
use Closure;
use Teapot\StatusCode;

class CrossOriginResourceSharing extends Wrapper
{
    const ALLOW_ORIGIN_WILDCARD = '*';
    const ALLOW_ORIGIN_HEADER = 'Access-Control-Allow-Origin';
    const EXPOSE_HEADERS_HEADER = 'Access-Control-Expose-Headers';
    const MAX_AGE_HEADER = 'Access-Control-Max-Age';
    const ALLOW_CRENDENTIALS_HEADER = 'Access-Control-Allow-Credentials';
    const ALLOW_METHODS_HEADER = 'Access-Control-Allow-Methods';
    const ALLOW_HEADERS_HEADER = 'Access-Control-Allow-Headers';
    const ORIGIN_HEADER = 'Origin';

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
            !$this->isValidHost($allowOrigin)
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
        $url = parse_url($host);
        return isset($url['scheme']) && isset($url['host']);
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
        if ($maxAge && (int) $maxAge == 0) {
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
        $this->readRequestHeaders($request);
        $response = $execution($request);
        $this->applyResponseHeaders($response);

        return $response;
    }

    protected function readRequestHeaders(Request $request)
    {
        $this->readOrigin($request);
    }

    protected function readOrigin(Request $request)
    {
        if ($this->allowOrigin === null) {
            return;
        }

        $header = $request->getHeader(self::ORIGIN_HEADER);
        if ($header) {
            if ($this->allowOrigin == self::ALLOW_ORIGIN_WILDCARD) {
                return;
            }

            if (in_array($header, (array) $this->allowOrigin)) {
                return;
            }    
        }

        throw new Forbidden();
    }

    protected function applyResponseHeaders(Response $response)
    {
        $this->applyAllowOriginHeader($response);
        $this->applyExposeHeaders($response);
        $this->applyMaxAge($response);
        $this->applyAllowCredentials($response);
        $this->applyAllowMethods($response);
        $this->applyAllowHeaders($response);
    }

    protected function applyAllowOriginHeader(Response $response)
    {
        if ($this->allowOrigin === null) {
            return;
        }

        foreach ((array) $this->allowOrigin as $origin) {
            $response->addHeader(self::ALLOW_ORIGIN_HEADER, $origin);
        }
    }

    protected function applyExposeHeaders(Response $response)
    {
        if ($this->exposeHeaders === null) {
            return;
        }

        $header = implode(', ',$this->exposeHeaders);
        $response->addHeader(self::EXPOSE_HEADERS_HEADER, $header);
    }

    protected function applyMaxAge(Response $response)
    {
        if ($this->maxAge === null) {
            return;
        }

        $response->addHeader(self::MAX_AGE_HEADER, $this->maxAge);
    }

    protected function applyAllowCredentials(Response $response)
    {
        if ($this->allowCredentials === null) {
            return;
        }

        $header = 'false';
        if ($this->allowCredentials) {
            $header = 'true';
        }

        $response->addHeader(self::ALLOW_CRENDENTIALS_HEADER, $header);
    }

    protected function applyAllowMethods(Response $response)
    {
        if ($this->allowMethods === null) {
            return;
        }

        $header = implode(', ',$this->allowMethods);
        $response->addHeader(self::ALLOW_METHODS_HEADER, $header);
    }

    protected function applyAllowHeaders(Response $response)
    {
        if ($this->allowHeaders === null) {
            return;
        }

        $header = implode(', ',$this->allowHeaders);
        $response->addHeader(self::ALLOW_HEADERS_HEADER, $header);
    }
}