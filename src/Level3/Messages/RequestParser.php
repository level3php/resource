<?php

namespace Level3\Messages;

use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\Request;

class RequestParser
{
    const HEADER_CONTENT_TYPE = 'content-type';

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
}