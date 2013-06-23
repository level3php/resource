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
use Level3\ResourceRepository\Getter;

class Builder
{
    private $repositoryHub;

    private $uri;
    private $data;
    private $links = array();
    private $embedded = array();

    public function __construct(RepositoryHub $hub)
    {
        $this->repositoryHub = $repositoryHub;
    }

    public function withURI($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function withEmbedded($rel, $key, $id)
    {
        $this->embedded[$rel][] = $this->hub[$key]->create($id);
        return $this;
    }

    public function withRelation($rel, $key, $id)
    {
        $uri = $this->getResouceURI($key, $id);

        $this->links[] = new Link($uri, $rel);
        return $this;
    }

    public function withLink($rel, $key, $method, $params, $templated = false)
    {
        $uri = $this->hub->getURI($key, $method, $params);

        $this->links[] = new Link($uri, $rel);
        return $this;
    }

    public function withData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function build()
    {
        $uri = $this->getResouceURI($this->key, $this->id);

        $resource = new Resource($uri);
        if ($this->data) $resource->setData($this->data);
        if ($this->links) $resource->setLinks($this->links, false, true);

        foreach($this->embedded as $rel => $embeddeds) {
            foreach($embeddeds as $embedded) {
                $resource->setEmbedded($rel, $embedded);
            }
        }

        return $resource;
    }

    private function getResouceURI($key, $id)
    {
        return $this->hub->getURI($key, 'get', array('id' => $id));
    }
}