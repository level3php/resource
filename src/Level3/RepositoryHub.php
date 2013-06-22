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
use Closure;
use Level3\Repository\Deleter;
use Level3\Repository\Getter;
use Level3\Repository\Finder;
use Level3\Repository\Poster;
use Level3\Repository\Putter;

class RepositoryHub
{
    const MIN_KEY_LENGHT = 1;

    private $repositoryDefinitions = array();
    private $instancedRepositories = array();

    public function registerDefinition($key, Closure $definition)
    {
        if ($this->isValidKey($key)) {
            $this->repositoryDefinitions[$key] = $definition;
        } 
    }

    public function get($key)
    {
        if (!isset($this->instancedRepositories[$key])) {
            $this->instanceDefinition($key);
        }

        return $this->instancedRepositories[$key];
    }

    public function getKeys()
    {
        return array_keys($this->repositoryDefinitions);
    }

    public function isFinder($key)
    {
        if ($this->get($key) instanceOf Finder) {
            return true;
        }

        return false;
    }

    public function isGetter($key)
    {
        if ($this->get($key) instanceOf Getter) {
            return true;
        }

        return false;
    }

    public function isPoster($key)
    {
        if ($this->get($key) instanceOf Poster) {
            return true;
        }

        return false;
    }

    public function isPutter($key)
    {
        if ($this->get($key) instanceOf Putter) {
            return true;
        }

        return false;
    }

    public function isDeleter($key)
    {
        if ($this->get($key) instanceOf Deleter) {
            return true;
        }

        return false;
    }

    private function instanceDefinition($key)
    {
        if (!isset($this->repositoryDefinitions[$key])) {
            throw new \UnexpectedValueException(
                sprintf('Unable to find a repository definition called "%s"', $key)
            );
        }

        $repository = $this->repositoryDefinitions[$key]();
        if (!$repository instanceOf Repository) {
            throw new \RuntimeException(
                sprintf('Invalid definition for "%s", must return a Repository instance', $key)
            );
        }

        $this->instancedRepositories[$key] = $repository;
    }

    private function isValidKey($key)
    {
        if (strlen($key) < self::MIN_KEY_LENGHT) {
            throw new \UnexpectedValueException(
                sprintf('Invalid resource key "%s", min length %d', $key, self::MIN_KEY_LENGHT)
            );
        }

        return true;
    }
}