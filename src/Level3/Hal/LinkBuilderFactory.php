<?php

namespace Level3\Hal;

use Level3\RepositoryMapper;

class LinkBuilderFactory
{
    private $repositoryMapper;

    public function __construct(RepositoryMapper $reposistoryMapper)
    {
        $this->repositoryMapper = $reposistoryMapper;
    }

    public function create()
    {
        return new LinkBuilder($this->repositoryMapper);
    }
}