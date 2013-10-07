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
use Level3\Repository\Patcher;

class Hub
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
        if (!$this->isDefinitionAlreadyInstanced($key)) {
            $this->failIfDefinitionNotExists($key);
            $this->instanceDefinition($key);
        }

        return $this->instancedRepositories[$key];
    }

    public function getKeys()
    {
        return array_keys($this->repositoryDefinitions);
    }

    private function instanceDefinition($key)
    {
        $repository = $this->repositoryDefinitions[$key]();
        if (!$repository instanceOf Repository) {
            throw new \RuntimeException(
                sprintf('Invalid definition for "%s", must return a Repository instance', $key)
            );
        }

        $this->setRegisteredRepositoryKeyToRepository($repository, $key);
        $this->instancedRepositories[$key] = $repository;
    }

    private function setRegisteredRepositoryKeyToRepository(Repository $repository, $key)
    {
        $repository->setKey($key);
    }

    private function isDefinitionAlreadyInstanced($key)
    {
        return isset($this->instancedRepositories[$key]);
    }

    private function failIfDefinitionNotExists($key)
    {
        if (!isset($this->repositoryDefinitions[$key])) {
            throw new \UnexpectedValueException(
                sprintf('Unable to find a repository definition called "%s"', $key)
            );
        }
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