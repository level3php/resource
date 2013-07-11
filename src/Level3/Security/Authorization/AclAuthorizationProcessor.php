<?php

namespace Level3\Security\Authorization;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Level3\Resources\ConfigParser;
use Level3\Resources\Exceptions\ConfigError;
use Level3\Security\Authentication\Credentials;

class AclAuthorizationProcessor extends AbstractAuthorizationProcessor
{
    private $config;

    public function __construct(
        RequestProcessor $processor,
        ConfigParser $configParser
    ) {
        parent::__construct($processor);
        $this->config = $configParser->getConfig();
        if (!isset($this->config['acl']) || !isset($this->config['acl']['routes'])) {
            throw new ConfigError('acl -> routes config sections have to be defined');
        }
    }

    protected function hasAccess(Request $request, $methodName)
    {
        $credentials = $request->getCredentials();

        foreach ($this->config['acl']['routes'] as $routeConfig) {
            if ($this->matches($request, $routeConfig)) {
                return $this->credentialsHasAccess($credentials, $routeConfig, $methodName, $request->getPathInfo());
            }
        }
        return false;
    }

    private function credentialsHasAccess(Credentials $credentials, $routeConfig, $methodName, $path)
    {
        if (!isset($routeConfig['policies'][$methodName])) {
            return $this->hasDefaultAccess($routeConfig);
        }

        $apiKey = $credentials->getApiKey();
        if (in_array($apiKey, $routeConfig['policies'][$methodName]['apiKeys'])) {
            return $this->matchingRequirementsMeet($credentials, $routeConfig, $methodName, $path);
        }

        return false;
    }

    private function matchingRequirementsMeet(Credentials $credentials, $routeConfig, $methodName, $path)
    {
        if (!isset($routeConfig['policies'][$methodName]['matchings'])) {
            return true;
        }
        $matchingConfig = $routeConfig['policies'][$methodName]['matchings'];

        $matches = array();
        $pathPattern = $routeConfig['path'];
        preg_match($pathPattern, $path, $matches);
        $method = $matchingConfig['credentialsMethod'];

        if (!method_exists($credentials, $method)) {
             throw new AclMatchingMethodNotFound($method);
        }

        return $matches[$matchingConfig['pathGroupMatch']] == $credentials->$method();
    }
}