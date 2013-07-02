<?php

namespace Level3\Messages;

use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\Request;

class RequestParser
{
    const HEADER_CONTENT_TYPE = 'content-type';
    const HEADER_RANGE = 'range';
    const HEADER_RANGE_UNIT_SEPARATOR = '=';
    const HEADER_RANGE_SEPARATOR = '-';

    private $parserFactory;

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    public function getRequestContentAsArray(Request $request)
    {
        $contentType = $request->getHeader(self::HEADER_CONTENT_TYPE);
        $parser = $this->parserFactory->create($contentType);

        return $parser->parse($request->getContent());
    }

    public function getRequestRange(Request $request)
    {
        if (!$request->hasHeader(self::HEADER_RANGE)) {
            return array(0,0);
        }

        $range = $request->getHeader(self::HEADER_RANGE);
        $range = $range[0];

        $range = explode(self::HEADER_RANGE_UNIT_SEPARATOR, $range);
        $range = $range[1];

        $range = explode(self::HEADER_RANGE_SEPARATOR, $range);

        if ('' === ($range[0])) {
            $range[0] = 0;
        }

        if ('' === $range[1]) {
            $range[1] = 0;
        }

        return $range;
    }
}