<?php

namespace Level3\Security\Authorization;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Level3\Resources\ConfigParser;
use Level3\Resources\Exceptions\ConfigError;

class RoleAuthorizationProcessor extends AbstractAuthorizationProcessor
{
    private $config;

    public function __construct(
        RequestProcessor $requestProcessor,
        ResponseFactory $responseFactory,
        ConfigParser $configParser
    ) {
        parent::__construct($requestProcessor, $responseFactory);
        $this->config = $configParser->getConfig();
        if (!isset($this->config['role']) || !isset($this->config['role']['routes'])) {
            throw new ConfigError('role -> routes config sections have to be defined');
        }
    }

    protected function hasAccess(Request $request, $methodName)
    {
        $role = $request->getCredentials()->getRole();

        foreach ($this->config['role']['routes'] as $routeConfig) {
            if ($this->matches($request, $routeConfig)) {
                return $this->roleHasAccess($role, $routeConfig, $methodName);
            }
        }
        return false;
    }

    private function matches(Request $request, array $routeConfig)
    {
        $pathExpression = $routeConfig['path'];
        $pathInfo = $request->getPathInfo();

        return preg_match($pathExpression, $pathInfo);
    }

    private function roleHasAccess(Role $role, array $route, $methodName)
    {
        if (!isset($route['policies'][$methodName])) {
            return $this->hasDefaultAccess($route);
        }

        $requiredRole = $route['policies'][$methodName]['role'];
        $hasAccess = $this->createHasAccessMethodName($requiredRole);
        return $role->$hasAccess();
    }

    private function hasDefaultAccess($route)
    {
        return $route['policies']['default'];
    }

    private function createHasAccessMethodName($requiredRole)
    {
        $methodName = sprintf('has%s', ucfirst($requiredRole));
        return $methodName;
    }
}