<?php

namespace Level3\Messages\Parser;

class JsonParser implements Parser
{
    public function parse($content)
    {
        return json_decode($content, true);
    }
}