<?php

namespace Level3\Messages;

use Level3\Resource\FormatterFactory;
use Level3\Processor\Wrapper\Authenticator\Credentials;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use Level3\Messages\Parameters;

class Request extends SymfonyRequest
{
    const HEADER_SORT = 'X-Sort';

    const HEADER_EXPAND = 'X-Expand-Links';
    const HEADER_EXPAND_PATH_SEPARATOR = ';';
    const HEADER_EXPAND_LEVEL_SEPARATOR = '.';

    const HEADER_RANGE = 'Range';
    const HEADER_RANGE_UNIT_SEPARATOR = '=';
    const HEADER_RANGE_SEPARATOR = '-';

    protected $availableHeaders = [
        self::HEADER_SORT,
        self::HEADER_RANGE,
        self::HEADER_EXPAND
    ];

    private $credentials;
    private $key;

    public function __construct($key, SymfonyRequest $origin)
    {
        $query = $request = $attributes = $cookies = $files = $server = null;

        $this->key = $key;

        $content = $origin->getContent();
        if ($origin->query) $query = $origin->query->all();
        if ($origin->request) $request = $origin->request->all();
        if ($origin->attributes) $attributes = $origin->attributes->all();
        if ($origin->cookies) $cookies = $origin->cookies->all();
        if ($origin->files) $files = $origin->files->all();
        if ($origin->server) $server = $origin->server->all();

        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function getKey()
    {
        return $this->key;
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

    public function setCredentials(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function getAttributes()
    {
        $params = new Parameters($this->attributes->all());
        $params->set('_credentials', $this->credentials);

        return $params;
    }

    public function getFilters()
    {
        return new Parameters([
            'range' => $this->getRange(),
            'criteria' => $this->getCriteria(),
            'sort' => $this->getSort(),
            'expand' => $this->getExpand()
        ]);
    }

    public function getRawContent($none = false)
    {
        return parent::getContent();
    }

    public function getContent($none = false)
    {
        $content = parent::getContent();

        return $this->getFormatter()->fromRequest($content);
    }

    public function getRange()
    {
        if (!$this->headers->has(self::HEADER_RANGE)) {
            return [0, 0];
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

    public function getExpand()
    {
        if (!$this->headers->has(self::HEADER_EXPAND)) {
            return [];
        }

        return $this->extractExpandFromHeader();
    }

    private function extractExpandFromHeader()
    {
        $expand = $this->headers->get(self::HEADER_EXPAND);

        $expand = explode(self::HEADER_EXPAND_PATH_SEPARATOR, $expand);
        foreach ($expand as &$part) {
            $part = explode(self::HEADER_EXPAND_LEVEL_SEPARATOR, $part);
        }

        return $expand;
    }

    public function isAuthenticated()
    {
        if ($this->credentials) {
            return $this->credentials->isAuthenticated();
        }
    }

    public function getHeader($header)
    {
        return $this->headers->get($header);
    }

    public function getCriteria()
    {
        $result = [];
        parse_str($this->getQueryString(), $result);

        return $result;
    }

    public function getSort()
    {
        if (!$this->headers->has(self::HEADER_SORT)) return null;

        $sortHeader = $this->headers->get(self::HEADER_SORT);

        return $this->parseSortHeader($sortHeader);
    }

    private function parseSortHeader($sortHeader)
    {
        $sort = [];
        $parts = explode(';', $sortHeader);
        foreach ($parts as $part) {
            list($field, $direction) = $this->parseSortPart($part);
            if ($field) $sort[$field] = $direction;
        }

        return $sort;
    }

    private function parseSortPart($part)
    {
        $match = [];
        $pattern = '/^
            \s* (?P<field>\w+) \s* # capture the field
            (?: = \s* (?P<direction>-?1) )? \s* # capture the sort direction if it is there
        $/x';
        preg_match($pattern, $part, $match);
        list($field, $direction) = $this->extractFieldAndDirectionFromRegexMatch($match);

        return [$field, $direction];
    }

    private function extractFieldAndDirectionFromRegexMatch($match)
    {
        if (isset($match['field'])) {
            $field = $match['field'];
        } else {
            $field = null;
        }

        if (isset($match['direction'])) {
            $direction = (int) $match['direction'];
        } else {
            $direction = 1;
        }

        return [$field, $direction];
    }
}
