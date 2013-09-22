<?php

namespace Level3\Messages;

use Level3\Resource\FormatterFactory;
use Level3\Security\Authentication\AuthenticatedCredentials;
use Level3\Security\Authentication\Credentials;
use Level3\Security\Authentication\User;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    const HEADER_RANGE = 'Range';
    const HEADER_RANGE_UNIT_SEPARATOR = '=';
    const HEADER_RANGE_SEPARATOR = '-';

    const HEADER_SORT = 'X-Level3-Sort';
    private $credentials;
    private $id;
    private $key;

    public function __construct($key, SymfonyRequest $origin)
    {       
        $this->key = $key;
        $query = $origin->query->all();
        $request = $origin->request->all();
        $attributes = $origin->attributes->all();
        $cookies = $origin->cookies->all();
        $files = $origin->files->all();
        $server = $origin->server->all();

        $this->initialize($query, $request, $attributes, $cookies, $files, $server);
        $this->credentials = new Credentials();
    }

    protected static function initializeFormats()
    {
        parent::initializeFormats();
        static::$formats['application/hal+json'] = array('application/hal+json');
        static::$formats['application/hal+xml'] = array('application/hal+xml');
    }

    public function getFormatter()
    {
        $contentTypes = $this->getAcceptableContentTypes();

        return $this->getFormatterFactory()->create($contentTypes, true);
    }

    protected function getFormatterFactory()
    {
        return new FormatterFactory();
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setCredentials(AuthenticatedCredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getAttributes()
    {
        return new Parameters($this->attributes->all());
    }

    public function getFilters()
    {
        return new Parameters(array(
            'range' => $this->getRange(),
            'criteria' => $this->getCriteria(),
            'sort' => $this->getSort()
        ));
    }

    public function getContentParsed()
    {

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

    public function getSort()
    {
        if (!$this->headers->has(self::HEADER_SORT)) return null;

        $sortHeader = $this->headers->get(self::HEADER_SORT);

        $sort = json_decode($sortHeader, true);
        if (!$sort) return null; // ToDo: throw exception to escalate to 403
        if (!is_array($sort)) {
            $sort = array($sort => 1);
        }
        return $sort;
    }
}
