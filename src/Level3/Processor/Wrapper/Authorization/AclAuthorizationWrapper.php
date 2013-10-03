<?php

namespace Level3\Processor\Wrapper\Authorization;

use Level3\Messages\Request;
use Level3\Resources\ConfigParser;
use Level3\Resources\Exceptions\ConfigError;
use Level3\Processor\Wrapper\Authentication\AuthenticatedCredentials;

class AclAuthorizationWrapper extends AbstractAuthorizationWrapper
{
    private $config;

    public function __construct(ConfigParser $configParser)
    {
        $this->config = $configParser->getConfig();
        if (!isset($this->config['acl']) || !isset($this->config['acl']['routes'])) {
            throw new ConfigError('acl -> routes config sections must be defined');
        }
    }

    protected function hasAccess(Request $request, $methodName)
    {
        $credentials = $request->getCredentials();
        if (!($credentials instanceof AuthenticatedCredentials)) return false;

        foreach ($this->config['acl']['routes'] as $routeConfig) {
            if ($this->matches($request, $routeConfig)) {
                return $this->credentialsHasAccess($credentials, $routeConfig, $methodName, $request->getPathInfo());
            }
        }
        return false;
    }

    private function credentialsHasAccess(AuthenticatedCredentials $credentials, $routeConfig, $methodName, $path)
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

    private function matchingRequirementsMeet(AuthenticatedCredentials $credentials, $routeConfig, $methodName, $path)
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
