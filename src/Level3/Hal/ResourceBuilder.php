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

class ResourceBuilder
{
    private $repositoryMapper;

    private $uri;
    private $data;
    private $links = array();
    private $embedded = array();

    public function __construct(RepositoryMapper $repositoryMapper)
    {
        $this->repositoryMapper = $repositoryMapper;
    }

    public function withURI($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function withEmbedded($relation, $repositoryKey, $id)
    {
        $resource = $this->getRepository($repositoryKey)->createResource($id);
        $this->embedded[$relation][] = $resource;

        return $this;
    }

    public function withLinkToResource($relation, $repositoryKey, $id)
    {
        $link = $this->getRepository($repositoryKey)->createLink($id);
        $this->links[$relation][] = $link;

        return $this;
    }

    public function withLink($relation, $repositoryKey, $method, $params, $templated = false)
    {
        $uri = $this->repositoryMapper->getURI($repositoryKey, $method, $params);
        $this->links[$relation] = new Link($uri, $relation);

        return $this;
    }

    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function build()
    {
        $resource = new Resource();
        if ($this->uri) $resource->setURI($this->uri);
        if ($this->data) $resource->setData($this->data);
        if ($this->links) $resource->setLinks($this->links, false, true);

        foreach($this->embedded as $rel => $embeddeds) {
            foreach($embeddeds as $embedded) {
                $resource->setEmbedded($rel, $embedded);
            }
        }

        return $resource;
    }

    private function getRepository($repositoryKey)
    {
        return $this->repositoryMapper->get($repositoryKey);
    }

    private function getResouceURI($repositoryKey, $id)
    {
        return $this->repositoryMapper->getURI($repositoryKey, 'get', array('id' => $id));
    }
}