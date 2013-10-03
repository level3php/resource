<?php

namespace Level3\Processor\Wrapper\Authorization;

use Level3\Exceptions\Forbidden;
use Level3\Messages\Request;
use Level3\Processor\Wrapper;

abstract class AbstractAuthorizationWrapper extends Wrapper
{
    public final function processRequest(Closure $execution, Request $request, $method)
    {
        if (!$this->hasAccess($request, $method)) {
            throw new Forbidden('Access Forbidden');
        }

        return $execution($request);
    }

    protected abstract function hasAccess(Request $request, $methodName);

    protected function matches(Request $request, array $routeConfig)
    {
        $pathExpression = $routeConfig['path'];
        $pathInfo = $request->getPathInfo();

        return preg_match($pathExpression, $pathInfo);
    }

    protected function hasDefaultAccess($route)
    {
        return isset($route['policies']['default']) && $route['policies']['default'];
    }
}
