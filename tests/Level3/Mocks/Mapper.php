<?php

namespace Level3\Mocks;
use Level3\MapperInterface;

class Mapper implements MapperInterface
{
    private $routes = array();

    public function mapList($uri, $alias)
    {
        $this->routes[$alias] = $uri;
    }

    public function mapGet($uri, $alias)
    {
        $this->routes[$alias] = $uri;
    }
    
    public function mapPost($uri, $alias)
    {
        $this->routes[$alias] = $uri;
    }
    
    public function mapPut($uri, $alias)
    {
        $this->routes[$alias] = $uri;
    }
    
    public function mapDelete($uri, $alias)
    {
        $this->routes[$alias] = $uri;
    }

    public function getURI($alias, array $parameters = null)
    {
        return $this->routes[$alias];
    }
}