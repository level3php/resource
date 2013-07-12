<?php

namespace Level3\Security\Authorization;

use Level3\Exceptions\Forbidden;
use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;

abstract class AbstractAuthorizationProcessor implements RequestProcessor
{
    protected $processor;

    public function __construct(RequestProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function find(Request $request)
    {
        return $this->processRequest($request, 'find');
    }

    private function processRequest(Request $request, $methodName)
    {
        if (!$this->hasAccess($request, $methodName)) {
            throw new Forbidden('Access Forbidden');
        }

        return $this->processor->$methodName($request);
    }

    protected abstract function hasAccess(Request $request, $methodName);

    public function get(Request $request)
    {
        return $this->processRequest($request, 'get');
    }

    public function post(Request $request)
    {
        return $this->processRequest($request, 'post');
    }

    public function put(Request $request)
    {
        return $this->processRequest($request, 'put');
    }

    public function delete(Request $request)
    {
        return $this->processRequest($request, 'delete');
    }

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