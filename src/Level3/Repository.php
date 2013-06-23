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

    public function createResourceLink($id)
    {

    }

    public function createResource($id)
    {
        if (!$this->repositoryMapper) {
            throw new \RuntimeException('Set a RepositoryMapper before using createResource method.');
        }

        $uri = $this->getRepositoryGetURI($id);

        $builder = new ResourceBuilder();
        $builder->withURI($uri);
        
        $resource->setData($this->resource($id));
        return $resource;
    }

    private function getRepositoryGetURI($id)
    {
        return $this->repositoryMapper->getURI($this->repositoryKey, 'get', array('id' => $id));
    }

    abstract protected function buildResource($id);
}