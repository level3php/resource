<?php

namespace Level3\Hal;

use Level3\RepositoryMapper;

class ResourceBuilderFactory
{
    private $repositoryMapper;
    private $linkBuilder;

    public function __construct(RepositoryMapper $repositoryMapper, LinkBuilder $linkBuilder)
    {
        $this->repositoryMapper = $repositoryMapper;
        $this->linkBuilder = $linkBuilder;
    }

    public function create()
    {
        return new ResourceBuilder($this->repositoryMapper, $this->linkBuilder);
    }
}