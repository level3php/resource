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
    private $linkBuilder;

    private $uri;
    private $data;
    private $links = array();
    private $embedded = array();

    public function __construct(RepositoryMapper $repositoryMapper, LinkBuilder $linkBuilder)
    {
        $this->repositoryMapper = $repositoryMapper;
        $this->linkBuilder = $linkBuilder;
        $this->linkBuilder->setRepositoryMapper($this->repositoryMapper);
    }

    public function clear()
    {
        $this->uri = null;
        $this->data = null;
        $this->links = array();
        $this->embedded = array();

        return $this;
    }

    public function withURI($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function withEmbedded($relation, $repositoryKey, $id)
    {
        $resource = $this->getRepository($repositoryKey)->get($id);
        $this->setSelfLinkToResource($resource, $repositoryKey, $id);

        $this->embedded[$relation][] = $resource;

        return $this;
    }

    public function withLinkToResource($relation, $repositoryKey, $id, $title = null)
    {
        $this->linkBuilder->clear()
            ->withResource($repositoryKey, $id)
            ->withName($id)
            ->withTitle($title);

        $this->links[$relation][] = $this->linkBuilder->build();

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

        foreach($this->embedded as $rel => $embeddeds) {
            foreach($embeddeds as $embedded) {
                $resource->addResource($rel, $embedded);
            }
        }

        foreach($this->links as $rel => $links) {
            foreach($links as $link) {
                $resource->addLink($rel, $link);
            }
        }
        
        return $resource;
    }

    private function setSelfLinkToResource(Resource $resource, $repositoryKey, $id)
    {
        try {
            $uri = $this->getResouceURI($repositoryKey, $id);
        } catch (\Exception $e) {
            $uri = null;
        }

        $resource->setURI($uri);
    }

    private function getRepository($repositoryKey)
    {
        return $this->repositoryMapper->getRepositoryHub()->get($repositoryKey);
    }

    private function getResouceURI($repositoryKey, $id)
    {
        return $this->repositoryMapper->getURI($repositoryKey, 'get', array('id' => $id));
    }
}