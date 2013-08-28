<?php

namespace Level3\Messages;

use Level3\Security\Authentication\AuthenticatedCredentials;
use Level3\Security\Authentication\Credentials;
use Level3\Security\Authentication\User;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    const HEADER_RANGE = 'Range';
    const HEADER_RANGE_UNIT_SEPARATOR = '=';
    const HEADER_RANGE_SEPARATOR = '-';
    private $credentials;
    private $id;
    private $key;

    public function __construct($key, SymfonyRequest $request)
    {
        $this->key = $key;
        $query = $request->query->all();
        $req = $request->request->all();
        $attributes = $request->attributes->all();
        $cookies = $request->cookies->all();
        $files = $request->files->all();
        $server = $request->server->all();

        $this->initialize($query, $req, $attributes, $cookies, $files, $server);
        $this->credentials = new Credentials();
    }

    protected static function initializeFormats()
    {
        parent::initializeFormats();
        static::$formats['application/hal+json'] = array('application/hal+json');
        static::$formats['application/hal+xml'] = array('application/hal+xml');
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setCredentials(AuthenticatedCredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getRange()
    {
        if (!$this->headers->has(self::HEADER_RANGE)) {
            return array(0, 0);
        }

        $range = $this->extractRangeFromHeader();

        if ('' === ($range[0])) {
            $range[0] = 0;
        }

        if ('' === $range[1]) {
            $range[1] = 0;
        }

        return $range;
    }

    private function extractRangeFromHeader()
    {
        $range = $this->headers->get(self::HEADER_RANGE);

        $range = explode(self::HEADER_RANGE_UNIT_SEPARATOR, $range);
        $range = $range[1];

        $range = explode(self::HEADER_RANGE_SEPARATOR, $range);
        return $range;
    }

    public function isAuthenticated()
    {
        return $this->credentials->isAuthenticated();
    }

    public function getHeader($header)
    {
        return $this->headers->get($header);
    }

    public function getCriteria()
    {
        $result = array();
        $parameters = explode('&', $this->getQueryString());
        foreach ($parameters as $parameter) {
            if (!strpos($parameter, '=')) break;
            $entry = explode('=', $parameter);
            $key = $entry[0];
            $value = $entry[1];
            $result[$key] = $value;
        }

        return $result;
    }
}