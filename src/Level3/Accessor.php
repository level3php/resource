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
use Level3\Messages\Parameters;

class Accessor
{
    private $repositoryHub;

    public function __construct(RepositoryHub $repositoryHub)
    {
        $this->repositoryHub = $repositoryHub;
    }

    public function find($key, Parameters $parameters, $sort, $lowerBound, $upperBound, $criteria)
    {
        $repository = $this->repositoryHub->get($key);
        return $repository->find($parameters, $sort, $lowerBound, $upperBound, $criteria);
    }

    public function get($key, Parameters $parameters)
    {
        $repository = $this->repositoryHub->get($key);
        return $repository->get($parameters);
    }

    public function post($key, Parameters $parameters, Array $receivedResourceData)
    {
        $repository = $this->repositoryHub->get($key);
        $repository->post($parameters, $receivedResourceData);
        return $repository->get($parameters);
    }

    public function put($key, Parameters $parameters, Array $receivedResourceData)
    {
        $repository = $this->repositoryHub->get($key);
        return $repository->put($parameters, $receivedResourceData);
    }

    public function delete($key, Parameters $parameters)
    {
        $repository = $this->repositoryHub->get($key);
        $repository->delete($parameters);
    }
}
