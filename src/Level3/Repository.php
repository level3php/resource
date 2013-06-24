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
use Level3\Hal\LinkBuilder;
use Level3\Hal\ResourceBuilder;

abstract class Repository
{
    private $repositoryMapper;
    private $repositoryKey;

    public function setKey($repositoryKey)
    {
        $this->repositoryKey = $repositoryKey;
    }

    public function getKey()
    {
        return $this->repositoryKey;
    }

    public function setRepositoryMapper(RepositoryMapper $repositoryMapper)
    {
        $this->repositoryMapper = $repositoryMapper;
    }

    public function getRepositoryMapper()
    {
        return $this->repositoryMapper;
    }

    public function getDescription()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));

        $description = substr($reflectionClass->getDocComment(), 3, -2);
        $description = trim(preg_replace('/\s*\*/', '', $description));
        return $description;
    }

    public function createLinkBuilder()
    {
        return new LinkBuilder($this->repositoryMapper);
    }

    public function createResourceBuilder()
    {
        return new ResourceBuilder($this->repositoryMapper);
    }
}