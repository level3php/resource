<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        if (!isset($this->routes[$alias])) return false;
        
        $uri = $this->routes[$alias];
        foreach($parameters as $key => $value) {
            $search = sprintf('{%s}', $key);
            $uri = str_replace($search, $value, $uri);
        }

        return $uri;
    }
}