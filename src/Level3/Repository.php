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
use Level3\Hal\ResourceBuilderFactory;

abstract class Repository
{
    private $repositoryKey;
    private $resourceBuilderFactory;

    public function __construct(ResourceBuilderFactory $resourceBuilderFactory)
    {
        $this->resourceBuilderFactory = $resourceBuilderFactory;
    }

    public function setKey($repositoryKey)
    {
        $this->repositoryKey = $repositoryKey;
    }

    public function getKey()
    {
        return $this->repositoryKey;
    }

    public function getDescription()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));

        $description = substr($reflectionClass->getDocComment(), 3, -2);
        $description = trim(preg_replace('/\s*\*/', '', $description));
        return $description;
    }

    protected function createResourceBuilder()
    {
        return $this->resourceBuilderFactory->create();
    }
}