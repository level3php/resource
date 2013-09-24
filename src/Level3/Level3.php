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
    private $debug;
    private $hub;
    private $mapper;
    private $processor;

    public function __construct(Mapper $mapper, Hub $hub, Processor $processor)
    {
        $this->hub = $hub;
        $this->mapper = $mapper;
        $this->processor = $processor;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function getyHub()
    {
        return $this->hub;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function getProcessor()
    {
        return $this->processor;
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