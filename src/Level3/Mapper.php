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

use Level3\Repository;
use Level3\Resource\Parameters;

abstract class Mapper
{
    const SLASH_CHARACTER = '/';

    protected $baseURI = self::SLASH_CHARACTER;
    protected $interfacesWithOutParams = array(
        'Level3\Repository\Putter' => 'PUT',
        'Level3\Repository\Finder' => 'GET'
    );

    protected $interfacesWithParams = array(
        'Level3\Repository\Deleter' => 'DELETE',
        'Level3\Repository\Getter' => 'GET',
        'Level3\Repository\Poster' => 'POST',
        'Level3\Repository\Patcher' => 'PATCH'
    );

    public function setBaseURI($uri)
    {
        if ($this->doesNotEndInSlash($uri)) {
            $uri = $this->addSlashToUri($uri);
        }

        $this->baseURI = $uri;
    }

    private function doesNotEndInSlash($uri)
    {
        return $uri[strlen($uri) - 1] != self::SLASH_CHARACTER;
    }

    private function addSlashToUri($uri)
    {
        return $uri . self::SLASH_CHARACTER;
    }

    public function getBaseURI()
    {
        return $this->baseURI;
    }

    private function transformCurieURI($curieURI, Parameters $parameters = null)
    {
        if (!$parameters) {
            return $curieURI;
        }

        foreach ($parameters->all() as $key => $value) {
            $curieURI = str_replace(sprintf('{%s}', $key), $value, $curieURI);
        }

        return $curieURI;
    }

    public function boot(Hub $hub)
    {
        foreach($hub->getKeys() as $resourceKey) {
            $this->mapRepositoryToRoutes($hub, $resourceKey);
        }
    }

    private function mapRepositoryToRoutes(Hub $hub, $repositoryKey)
    {
        $repository = $hub->get($repositoryKey);

        $interfaces = array_merge(
            $this->interfacesWithOutParams, 
            $this->interfacesWithParams
        );

        foreach ($interfaces as $interface => $method) {
            $this->mapMethodIfNeeded($repository, $interface);
        }
    }

    private function mapMethodIfNeeded(Repository $repository, $interface)
    {
        if ($repository instanceOf $interface) {
            $this->callToMapMethod($repository, $interface);
        }
    }

    private function callToMapMethod(Repository $repository, $interface)
    {
        $namespace = explode('\\', $interface);
        $name = ucfirst(strtolower(end($namespace)));
        $method = sprintf('map%s', $name);

        $curieURI = $this->getCurieURI($repository->getKey(), $interface);

        $this->$method($repository, $curieURI);
    }

    public function getURI($repositoryKey, $interface, Parameters $parameters = null)
    {
        $curieURI = $this->getCurieURI($repositoryKey, $interface);

        return $this->transformCurieURI($curieURI, $parameters);
    }

    public function getCurieURI($repositoryKey, $requestedInterface)
    {
        foreach ($this->interfacesWithOutParams as $interface => $method) {
            if ($interface == $requestedInterface) {
                return $this->getCurieURIWithOutParams($repositoryKey);
            }
        }

        foreach ($this->interfacesWithParams as $interface => $method) {
            if ($interface == $requestedInterface) {
                return $this->getCurieURIWithParams($repositoryKey);
            }
        }
    }

    protected function getCurieURIWithOutParams($repositoryKey)
    {
        return $this->baseURI . $repositoryKey;
    }

    protected function getCurieURIWithParams($repositoryKey)
    {
        return $this->getCurieURIWithOutParams($repositoryKey) . '/{id}';
    }

    abstract public function mapFinder($repository, $uri);
    abstract public function mapGetter($repository, $uri);
    abstract public function mapPoster($repository, $uri);
    abstract public function mapPutter($repository, $uri);
    abstract public function mapPatcher($repository, $uri);
    abstract public function mapDeleter($repository, $uri);
}