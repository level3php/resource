<?php

namespace Level3\Resources;

use Level3\Resources\Exceptions\ConfigParseError;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlConfigParser implements ConfigParser
{
    private $filePath;

    public function __construct($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf('File not found: %s', $filePath));
        }

        $this->filePath = $filePath;
    }

    public function getConfig()
    {
        $fileContent = file_get_contents($this->filePath);
        $yamlParser = new Parser();

        try {
            return $yamlParser->parse($fileContent);
        } catch (ParseException $e) {
            throw new ConfigParseError($e->getMessage(), $e->getCode(), $e);
        }
    }
}