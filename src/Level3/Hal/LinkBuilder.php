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

    public function __construct(RepositoryMapper $repositoryMapper)
    {
        $this->repositoryMapper = $repositoryMapper;
    }

    public function withResource($repositoryKey, $id)
    {
        $this->href = $this->getResouceURI($repositoryKey, $id);

        return $this;
    }

    public function build()
    {
        $resource = new Link();
        if ($this->href) $resource->setHref($this->href);

        return $resource;
    }

    private function getResouceURI($repositoryKey, $id)
    {
        return $this->repositoryMapper->getURI($repositoryKey, 'get', array('id' => $id));
    }
}