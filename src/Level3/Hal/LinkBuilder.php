<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Hal;
use Level3\RepositoryMapper;

class LinkBuilder
{
    private $repositoryMapper;

    private $href;
    private $name;
    private $title;

    public function setRepositoryMapper(RepositoryMapper $repositoryMapper)
    {
        $this->repositoryMapper = $repositoryMapper;
    }

    public function clear()
    {
        $this->href = null;
        $this->name = null;
        $this->title = null;

        return $this;
    }

    public function withResource($repositoryKey, $id)
    {
        $this->href = $this->getResouceURI($repositoryKey, $id);

        return $this;
    }

    public function withTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function build()
    {
        $resource = new Link();
        if ($this->href) $resource->setHref($this->href);
        if ($this->name) $resource->setName($this->name);
        if ($this->title) $resource->setTitle($this->title);

        return $resource;
    }

    private function getResouceURI($repositoryKey, $id)
    {
        return $this->repositoryMapper->getURI($repositoryKey, 'get', array('id' => $id));
    }
}