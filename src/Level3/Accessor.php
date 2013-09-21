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

use Level3\Resource\Parameters;

class Accessor
{
    private $level3;

    public function __construct(Level3 $level3)
    {
        $this->level3 = $level3;
    }

    public function find($key, Parameters $parameters, $sort, $lowerBound, $upperBound, $criteria)
    {
        $repository = $this->level3->getRepository($key);
        return $repository->find($parameters, $sort, $lowerBound, $upperBound, $criteria);
    }

    public function get($key, Parameters $parameters)
    {
        $repository = $this->level3->getRepository($key);
        return $repository->get($parameters);
    }

    public function post($key, Parameters $parameters, Array $receivedResourceData)
    {
        $repository = $this->level3->getRepository($key);
        $repository->post($parameters, $receivedResourceData);
        return $repository->get($parameters);
    }

    public function patch($key, Parameters $parameters, Array $receivedResourceData)
    {
        $repository = $this->level3->getRepository($key);
        $repository->patch($parameters, $receivedResourceData);
        return $repository->get($parameters);
    }
    
    public function put($key, Parameters $parameters, Array $receivedResourceData)
    {
        $repository = $this->level3->getRepository($key);
        return $repository->put($parameters, $receivedResourceData);
    }

    public function delete($key, Parameters $parameters)
    {
        $repository = $this->level3->getRepository($key);
        $repository->delete($parameters);
    }
}
