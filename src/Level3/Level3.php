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
use Level3\Processor\Wrapper;

class Level3
{
    const PRIORITY_LOW = 10;
    const PRIORITY_NORMAL = 20;
    const PRIORITY_HIGH = 30;

    private $debug;
    private $hub;
    private $mapper;
    private $processor;
    private $wrappers = array();

    public function __construct(Mapper $mapper, Hub $hub, Processor $processor)
    {
        $this->hub = $hub;
        $this->mapper = $mapper;
        $this->processor = $processor;

        $this->setLevel3ToObject($processor);
        $this->setLevel3ToObject($hub);
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function getHub()
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

    public function getURI($repositoryKey, $interface = null, Parameters $parameters = null)
    {
        return $this->mapper->getURI($repositoryKey, $interface, $parameters);
    }

    public function clearProcessWrappers()
    {
        $this->wrappers = array();
    }

    public function addProcessorWrapper(Wrapper $wrapper, $priority = self::PRIORITY_NORMAL)
    {
        $this->wrappers[$priority][] = $wrapper;
        $this->setLevel3ToObject($wrapper);
    }

    protected function setLevel3ToObject($object)
    {
        $object->setLevel3($this);
    }

    public function getProcessorWrappers()
    {
        $result = array();
        
        ksort($this->wrappers);
        foreach ($this->wrappers as $priority => $wrappers) {
            $result = array_merge($result, $wrappers);
        }

        return $result;
    }

    public function boot()
    {
        $this->mapper->boot($this->getHub());
    }
}