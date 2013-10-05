<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3;

use Level3\Resource;
use Level3\Resource\Parameters;
use ReflectionClass;

abstract class Repository
{
    private $repositoryKey;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function getLevel3()
    {
        return $this->level3;
    }
    
    public function setKey($repositoryKey)
    {
        $this->repositoryKey = $repositoryKey;
    }

    public function getKey()
    {
        return $this->repositoryKey;
    }

    public function getURI(Parameters $attributes = null, $method = null)
    {   
        $key = $this->getKey();

        return $this->level3->getURI($key, $method, $attributes);
    }

    public function getDescription()
    {
        $reflectionClass = new ReflectionClass(get_class($this));

        $description = substr($reflectionClass->getDocComment(), 3, -2);
        $description = trim(preg_replace('/\s*\*/', '', $description));
        return $description;
    }

    public function createResource(Parameters $attributes)
    {
        $uri = $this->getURI($attributes);

        $resource = new Resource();
        $resource->setURI($uri);
    }
}