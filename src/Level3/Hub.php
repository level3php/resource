<?php

namespace Level3;
use Closure;

class Hub
{
    const MIN_KEY_LENGHT = 1;

    private $level3;
    private $repositoryDefinitions = [];
    private $instancedRepositories = [];

    public function setLevel3(Level3 $level3)
    {
        $this->level3 = $level3;
    }

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
        $repository = $this->repositoryDefinitions[$key]($this->level3);
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
