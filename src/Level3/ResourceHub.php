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
use Level3\ResourceRepository;
use Level3\ResourceRepository\Deleter;
use Level3\ResourceRepository\Getter;
use Level3\ResourceRepository\Finder;
use Level3\ResourceRepository\Poster;
use Level3\ResourceRepository\Putter;


class ResourceHub extends Pimple
{
    const SLASH_CHARACTER = '/';

    private $mapper;
    private $baseURI = self::SLASH_CHARACTER;

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
        if ($this->doesNotEndInSlash($uri)) {
            $uri = $this->addSlashToUri($uri);
        }

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
        $this->registerDefaultsBeforeBoot();
        foreach($this->keys() as $key) {
            $this->validate($key);
            $this->map($key);
        }
    }

    public function registerDefaultsBeforeBoot()
    {
        $this['resources'] = new Resources();
        $this->mapper->mapRootTo('/resources');
    }

    private function validate($key)
    {
        $rm = $this[$key];
        if ( !is_object($rm) || !$rm instanceOf ResourceRepository ) {
            throw new \UnexpectedValueException(
                sprintf('The resource "%s" must return a ResourceRepository instance', $key)
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

        if ($rm instanceOf Finder) {
            $this->mapper->mapFind($generalURI, sprintf('%s:find', $key));
        }

        if ($rm instanceOf Getter) {
            $this->mapper->mapGet($particularURI, sprintf('%s:get', $key));
        }

        if ($rm instanceOf Poster) {
            $this->mapper->mapPost($particularURI, sprintf('%s:post', $key));
        }

        if ($rm instanceOf Putter) {
            $this->mapper->mapPut($generalURI, sprintf('%s:put', $key));
        }

        if ($rm instanceOf Deleter) {
            $this->mapper->mapDelete($particularURI, sprintf('%s:delete', $key));
        }
    }

    public function doesNotEndInSlash($uri)
    {
        return $uri[strlen($uri) - 1] != self::SLASH_CHARACTER;
    }

    private function addSlashToUri($uri)
    {
        return $uri . self::SLASH_CHARACTER;
    }
}