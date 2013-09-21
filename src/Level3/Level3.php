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

class Level3
{
    private $hub;
    private $mapper;
    private $accessor;

    public function __construct(Mapper $mapper, Hub $hub, Accessor $accessor)
    {
        $this->hub = $hub;
        $this->mapper = $mapper;
        $this->accessor = $accesor;
    }

    public function getyHub()
    {
        return $this->hub;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function getAccessor()
    {
        return $this->accessor;
    }

    public function getRepository($repositoryKey)
    {
        return $this->hub->get($repositoryKey);
    }

    public function getURI($repositoryKey, $interface, Parameters $parameters = null)
    {
        return $this->mapper->getURI($repositoryKey, $interface, $parameters);
    }
}