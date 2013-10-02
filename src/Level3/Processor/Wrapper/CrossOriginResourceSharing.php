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

    const HEADER_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    const HEADER_EXPOSE_HEADERS = 'Access-Control-Expose-Headers';
    const HEADER_MAX_AGE = 'Access-Control-Max-Age';
    const HEADER_ALLOW_CRENDENTIALS = 'Access-Control-Allow-Credentials';
    const HEADER_ALLOW_METHODS = 'Access-Control-Allow-Methods';
    const HEADER_ALLOW_HEADERS = 'Access-Control-Allow-Headers';
    const HEADER_ORIGIN = 'Origin';

    protected $enabledHeaders = array(
        self::HEADER_ALLOW_ORIGIN => array(),
        self::HEADER_ALLOW_CRENDENTIALS => array(),
        self::HEADER_EXPOSE_HEADERS => array(),
        self::HEADER_MAX_AGE => array('options'),
        self::HEADER_ALLOW_HEADERS => array('options'),
        self::HEADER_ALLOW_METHODS => array('options'),
    );

    protected $allowOrigin;
    protected $exposeHeaders;
    protected $maxAge;
    protected $allowCredentials;
    protected $allowMethods;
    protected $allowHeaders = array(
        Request::HEADER_RANGE,
        Request::HEADER_SORT
    );

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
        $this->readRequestHeaders($request, $method);

        $response = $execution($request);
        $this->applyResponseHeaders($response, $method);

        return $response;
    }

    protected function readRequestHeaders(Request $request, $method)
    {
        $this->readOrigin($request);
    }

    protected function readOrigin(Request $request)
    {
        if ($this->allowOrigin === null) {
            return;
        }

        $header = $request->getHeader(self::HEADER_ORIGIN);
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

    protected function applyResponseHeaders(Response $response, $method)
    {
        $this->applyAllowOriginHeader($response, $method);
        $this->applyAllowMethods($response, $method);
        $this->applyAllowHeaders($response, $method);
        $this->applyMaxAge($response, $method);
        $this->applyExposeHeaders($response, $method);
        $this->applyAllowCredentials($response, $method);
    }

    protected function applyAllowOriginHeader(Response $response, $method)
    {
        if ($this->allowOrigin === null) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_ALLOW_ORIGIN, $method)) {
            return;
        }

        foreach ((array) $this->allowOrigin as $origin) {
            $response->addHeader(self::HEADER_ALLOW_ORIGIN, $origin);
        }
    }

    protected function applyExposeHeaders(Response $response, $method)
    {
        if (!$this->isHeaderEnabled(self::HEADER_EXPOSE_HEADERS, $method)) {
            return;
        }

        $nonBasicHeaders = $this->getNonSimpleResponseHeaders($response);
        if ($this->exposeHeaders !== null) {
            $exposeHeaders = array_intersect(
                $this->exposeHeaders,
                $nonBasicHeaders
            );
        } else {
            $exposeHeaders = $nonBasicHeaders;
        }

        $header = implode(', ', $exposeHeaders);
        $response->addHeader(self::HEADER_EXPOSE_HEADERS, $header);
    }

    protected function getNonSimpleResponseHeaders(Response $response)
    {
        $simpleHeaders = array(
            'cache-control', 'content-language', 'content-type',
            'expires' , 'last-modified', 'pragma', 'status', 'date',
            self::HEADER_ALLOW_ORIGIN, self::HEADER_EXPOSE_HEADERS, 
            self::HEADER_MAX_AGE, self::HEADER_ALLOW_CRENDENTIALS, 
            self::HEADER_ALLOW_METHODS, self::HEADER_ALLOW_HEADERS
        );

        array_walk($simpleHeaders, function(&$value) { $value = strtolower($value); });

        $allHeaders = $response->headers->keys();
        return array_diff($allHeaders, $simpleHeaders);
    }

    protected function applyMaxAge(Response $response, $method)
    {
        if ($this->maxAge === null) {
            return;
        }
        
        if (!$this->isHeaderEnabled(self::HEADER_MAX_AGE, $method)) {
            return;
        }

        $response->addHeader(self::HEADER_MAX_AGE, $this->maxAge);
    }

    protected function applyAllowCredentials(Response $response, $method)
    {
        if ($this->allowCredentials === null) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_ALLOW_CRENDENTIALS, $method)) {
            return;
        }

        $header = 'false';
        if ($this->allowCredentials) {
            $header = 'true';
        }

        $response->addHeader(self::HEADER_ALLOW_CRENDENTIALS, $header);
    }

    protected function applyAllowMethods(Response $response, $method)
    {
        if ($this->allowMethods === null) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_ALLOW_METHODS, $method)) {
            return;
        }

        $header = implode(', ',$this->allowMethods);
        $response->addHeader(self::HEADER_ALLOW_METHODS, $header);
    }

    protected function applyAllowHeaders(Response $response, $method)
    {
        if ($this->allowHeaders === null) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_ALLOW_HEADERS, $method)) {
            return;
        }

        $header = implode(', ',$this->allowHeaders);
        $response->addHeader(self::HEADER_ALLOW_HEADERS, $header);
    }

    protected function isHeaderEnabled($header, $method)
    {
        if (isset($this->enabledHeaders[$header])) {
            $config = $this->enabledHeaders[$header];
            if (count($config) == 0 || in_array($method, $config)) {
                return true;
            }
        }

        return false;
    }
}