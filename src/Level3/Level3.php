<?php

namespace Level3;

use Level3\Messages\Parameters;
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
    private $wrappers = [];

    public function __construct(Mapper $mapper, Hub $hub, Processor $processor)
    {
        $this->hub = $hub;
        $this->mapper = $mapper;
        $this->processor = $processor;

        $processor->setLevel3($this);
        $hub->setLevel3($this);
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
        $this->wrappers = [];
    }

    public function addProcessorWrapper(Wrapper $wrapper, $priority = self::PRIORITY_NORMAL)
    {
        $this->wrappers[$priority][] = $wrapper;
        $wrapper->setLevel3($this);
    }

    public function getProcessorWrappers()
    {
        $result = [];

        ksort($this->wrappers);
        foreach ($this->wrappers as $priority => $wrappers) {
            $result = array_merge($result, $wrappers);
        }

        return $result;
    }

    public function getProcessorWrappersByClass($class)
    {
        foreach ($this->getProcessorWrappers() as $wrapper) {
            if ($wrapper instanceof $class) {
                return $wrapper;
            }
        }
    }

    public function boot()
    {
        $this->mapper->boot($this->getHub());
    }
}
