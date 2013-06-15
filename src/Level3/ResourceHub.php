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

use Pimple;
use Level3\ResourceManager;
use Level3\ResourceManager\DeleteInterface;
use Level3\ResourceManager\GetInterface;
use Level3\ResourceManager\FindInterface;
use Level3\ResourceManager\PostInterface;
use Level3\ResourceManager\PutInterface;


class ResourceHub extends Pimple {
    private $mapper;
    private $baseURI = '/';

    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function setBaseURI($uri)
    {
        if ( $uri[strlen($uri)-1] != '/' ) $uri .= '/';
        $this->baseURI = $uri;
    }

    public function getBaseURI()
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
            $this->validate($key);
            $this->map($key);
        }
    }

    private function validate($key)
    {
        $rm = $this[$key];
        if ( !is_object($rm) || !$rm instanceOf ResourceManager ) {
            throw new \UnexpectedValueException(
                sprintf('The resource "%s" must return a ResourceManager instance', $key)
            );
        }
    }

    private function map($key)
    {
        $rm = $this[$key];
        $rm->setHub($this);
        $rm->setKey($key);

        $generalURI = $this->baseURI . $key;
        $particularURI = $this->baseURI . $key . '/{id}';

        if ($rm instanceOf GetInterface) {
            $this->mapper->mapFind($generalURI, sprintf('%s:find', $key));
        }

        if ($rm instanceOf GetInterface) {
            $this->mapper->mapGet($particularURI, sprintf('%s:get', $key));
        }

        if ($rm instanceOf PostInterface) {
            $this->mapper->mapPost($particularURI, sprintf('%s:post', $key));
        }

        if ($rm instanceOf PutInterface) {
            $this->mapper->mapPut($generalURI, sprintf('%s:put', $key));
        }

        if ($rm instanceOf DeleteInterface) {
            $this->mapper->mapDelete($particularURI, sprintf('%s:delete', $key));
        }
    }
}