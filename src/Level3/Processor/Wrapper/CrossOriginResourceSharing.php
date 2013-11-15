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

    protected $enabledHeaders = [
        self::HEADER_ALLOW_ORIGIN => [],
        self::HEADER_ALLOW_CRENDENTIALS => [],
        self::HEADER_EXPOSE_HEADERS => [],
        self::HEADER_MAX_AGE => ['options'],
        self::HEADER_ALLOW_HEADERS => ['options'],
        self::HEADER_ALLOW_METHODS => ['options'],
    ];

    protected $allowOrigin = self::ALLOW_ORIGIN_WILDCARD;
    protected $exposeHeaders;
    protected $maxAge;
    protected $allowCredentials;
    protected $allowMethods = true;
    protected $allowHeaders = [
        Request::HEADER_RANGE,
        Request::HEADER_SORT,
        request::HEADER_EXPAND
    ];

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
        $this->exposeHeaders = array_map('strtolower', $exposeHeaders);
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

    public function setAllowMethods($allowMethods)
    {
        if ($allowMethods !== true && $allowMethods !== false) {
            throw new RuntimeException('Malformed allow-methods, must be a boolean');
        }

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

    public function options(Closure $execution, Request $request)
    {
        $response = new Response();
        $response->setStatusCode(StatusCode::NO_CONTENT);
        $this->applyResponseHeaders($request, $response, 'options');

        return $response;
    }

    protected function processRequest(Closure $execution, Request $request, $method)
    {
        $this->readAndCheckRequestHeaders($request, $method);

        $response = $execution($request);
        $this->applyResponseHeaders($request, $response, $method);

        return $response;
    }

    protected function readAndCheckRequestHeaders(Request $request, $method)
    {
        $this->readOriginAndValidate($request, $method);
    }

    protected function readOriginAndValidate(Request $request, $method)
    {
        if (
            $this->allowOrigin === null ||
            $this->allowOrigin == self::ALLOW_ORIGIN_WILDCARD
        ) {
            return;
        }

        $header = $request->getHeader(self::HEADER_ORIGIN);
        if ($header && in_array($header, (array) $this->allowOrigin)) {
            return;
        }

        if ($method != 'error') {
            throw new Forbidden();
        }
    }

    protected function applyResponseHeaders(Request $request, Response $response, $method)
    {
        $this->applyAllowOriginHeader($request, $response, $method);
        $this->applyAllowMethods($request, $response, $method);
        $this->applyAllowHeaders($request, $response, $method);
        $this->applyMaxAge($request, $response, $method);
        $this->applyExposeHeaders($request, $response, $method);
        $this->applyAllowCredentials($request, $response, $method);
    }

    protected function applyAllowOriginHeader(Request $request, Response $response, $method)
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

    protected function applyExposeHeaders(Request $request, Response $response, $method)
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

        array_walk($exposeHeaders, function (&$value) {
            $value = implode('-', array_map('ucfirst', explode('-', $value)));
        });

        $header = implode(', ', $exposeHeaders);
        $response->addHeader(self::HEADER_EXPOSE_HEADERS, $header);
    }

    protected function getNonSimpleResponseHeaders(Response $response)
    {
        $simpleHeaders = [
            'Cache-control', 'Content-Language', 'Content-Type',
            'Expires' , 'Last-Modified', 'Pragma', 'Status', 'Date',
            self::HEADER_ALLOW_ORIGIN, self::HEADER_EXPOSE_HEADERS,
            self::HEADER_MAX_AGE, self::HEADER_ALLOW_CRENDENTIALS,
            self::HEADER_ALLOW_METHODS, self::HEADER_ALLOW_HEADERS
        ];

        array_walk($simpleHeaders, function (&$value) {
            $value = strtolower($value);
        });

        $allHeaders = $response->headers->keys();

        $headers = array_diff($allHeaders, $simpleHeaders);

        return $headers;
    }

    protected function applyMaxAge(Request $request, Response $response, $method)
    {
        if ($this->maxAge === null) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_MAX_AGE, $method)) {
            return;
        }

        $response->addHeader(self::HEADER_MAX_AGE, $this->maxAge);
    }

    protected function applyAllowCredentials(Request $request, Response $response, $method)
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

    protected function applyAllowMethods(Request $request, Response $response, $method)
    {
        if (!$this->allowMethods) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_ALLOW_METHODS, $method)) {
            return;
        }

        $methods = $this->getAvailableMethods($request);

        $header = implode(', ', $methods);
        $response->addHeader(self::HEADER_ALLOW_METHODS, $header);
    }

    protected function getAvailableMethods(Request $request)
    {
        $key = $request->getKey();
        $repository = $this->getLevel3()->getHub()->get($key);

        return $this->getLevel3()->getMapper()->getMethods($repository);
    }

    protected function applyAllowHeaders(Request $request, Response $response, $method)
    {
        if ($this->allowHeaders === null) {
            return;
        }

        if (!$this->isHeaderEnabled(self::HEADER_ALLOW_HEADERS, $method)) {
            return;
        }

        $header = implode(', ', $this->allowHeaders);
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
