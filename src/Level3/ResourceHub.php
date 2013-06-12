<?php
namespace Level3;

use Pimple;
use Level3\Resources\DeleteInterface
use Level3\Resources\GetInterface
use Level3\Resources\PostInterface
use Level3\Resources\PutInterface


class ResourceHub extends Pimple {
    private $mapper;
    private $baseURI;

    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper($uri)
    {
        return $this->mapper;
    }

    public function getBaseURI($uri)
    {
        return $this->baseURI;
    }

    public function setBaseURI($uri)
    {
        $this->baseURI = $uri;
        if ( $this->baseURI[count($this->baseURI)-1] != '/' ) $this->baseURI .= '/';
    }

    public function getBaseURI($uri)
    {
        return $this->baseURI;
    }

    public function getURI($key, $method, array $parameters = array())
    {
        return $this->mapper->getURI(sprintf('%s:%s', $key, $method), $parameters);
    }

    public function boot()
    {
        foreach($this->keys() as $key) {
            $this->map($app, $key);
        }
    }

    public function map($key)
    {
        $generalURI = $this->baseURI . $uri;
        $particularURI = $this->baseURI . $uri . '/{id}';

        if ($this[$key] instanceOf GetInterface) {
            $this->mapper->mapList($generalURI, sprintf('%s:list', $key));
            $this->mapper->mapGet($generalURI, sprintf('%s:get', $key);
        }

        if ($this[$key] instanceOf PostInterface) {
            $this->mapper->mapPost($generalURI, sprintf('%s:post', $key));
        }

        if ($this[$key] instanceOf PutInterface) {
            $this->mapper->mapPut($generalURI, sprintf('%s:put', $key));
        }

        if ($this[$key] instanceOf DeleteInterface) {
            $this->mapper->mapDelete($generalURI, sprintf('%s:delete', $key));
        }
    }
}