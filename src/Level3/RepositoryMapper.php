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

abstract class RepositoryMapper
{
    const SLASH_CHARACTER = '/';

    private $repositoryHub;
    private $baseURI = self::SLASH_CHARACTER;

    public function __construct(RepositoryHub $repositoryHub)
    {
        $this->repositoryHub = $repositoryHub;
    }

    public function getRepositoryHub()
    {
        return $this->repositoryHub;
    }

    public function setBaseURI($uri)
    {
        if ($this->doesNotEndInSlash($uri)) {
            $uri = $this->addSlashToUri($uri);
        }

        $this->baseURI = $uri;
    }

    public function getBaseURI()
    {
        return $this->baseURI;
    }

    abstract public function getURI($repositoryKey, $method, array $parameters = array());

    public function boot()
    {
        foreach($this->repositoryHub->getKeys() as $resourceKey) {
            $this->mapRepositoryToRoutes($resourceKey);
        }
    }

    private function mapRepositoryToRoutes($repositoryKey)
    {
        $generalURI = $this->calculateGeneralUri($repositoryKey);
        $particularURI = $this->calculateParticularUri($repositoryKey);

        if ($this->repositoryHub->isFinder($repositoryKey)) {
            $this->mapFind($repositoryKey, $generalURI);
        }

        if ($this->repositoryHub->isGetter($repositoryKey)) {
            $this->mapGet($repositoryKey, $particularURI);
        }

        if ($this->repositoryHub->IsPoster($repositoryKey)) {
            $this->mapPost($repositoryKey, $particularURI);
        }

        if ($this->repositoryHub->isPutter($repositoryKey)) {
            $this->mapPut($repositoryKey, $generalURI);
        }

        if ($this->repositoryHub->isDeleter($repositoryKey)) {
            $this->mapDelete($repositoryKey, $particularURI);
        }
    }

    abstract public function mapFind($repositoryKey, $uri);
    abstract public function mapGet($repositoryKey, $uri);
    abstract public function mapPost($repositoryKey, $uri);
    abstract public function mapPut($repositoryKey, $uri);
    abstract public function mapDelete($repositoryKey, $uri);

    private function calculateGeneralUri($repositoryKey)
    {
        return $this->baseURI . $repositoryKey;
    }

    private function calculateParticularUri($repositoryKey)
    {
        return $this->calculateGeneralUri($repositoryKey) . '/{id}';
    }

    private function doesNotEndInSlash($uri)
    {
        return $uri[strlen($uri) - 1] != self::SLASH_CHARACTER;
    }

    private function addSlashToUri($uri)
    {
        return $uri . self::SLASH_CHARACTER;
    }
}