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

class Accessor
{
    private $repositoryHub;

    public function __construct(RepositoryHub $repositoryHub)
    {
        $this->repositoryHub = $repositoryHub;
    }

    public function find($key, $sort, $lowerBound, $upperBound, $criteria)
    {
        $repository = $this->repositoryHub->get($key);
        return $repository->find($sort, $lowerBound, $upperBound, $criteria);
    }

    public function get($key, $id)
    {
        $repository = $this->repositoryHub->get($key);
        return $repository->get($id);
    }

    public function post($key, $id, Array $receivedResourceData)
    {
        $repository = $this->repositoryHub->get($key);
        $repository->post($id, $receivedResourceData);
        return $repository->get($id);
    }

    public function put($key, Array $receivedResourceData)
    {
        $repository = $this->repositoryHub->get($key);
        return $repository->put($receivedResourceData);
    }

    public function delete($key, $id)
    {
        $repository = $this->repositoryHub->get($key);
        $repository->delete($id);
    }
}
