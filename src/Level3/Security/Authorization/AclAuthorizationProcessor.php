<?php

namespace Level3\Security\Authorization;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Resources\ConfigParser;

class AclAuthorizationProcessor extends AbstractAuthorizationProcessor
{
    private $configuration;

    public function __construct(RequestProcessor $processor, ConfigParser $configParser)
    {
        parent::__construct($processor);
        $this->configuration = $configParser->getConfig();
    }

    public function hasAccess(Request $request, $methodName)
    {

    }
}