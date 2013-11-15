<?php

namespace Level3\Resource\Formatter\HAL;

use Level3\Resource\Formatter\JsonFormatter as BaseJsonFormatter;
use Level3\Resource\Resource;

class JsonFormatter extends BaseJsonFormatter
{
    const CONTENT_TYPE = 'application/hal+json';

    protected function resourceToArray(Resource $resource)
    {
        $data = $resource->getData();

        $this->transformResources($data, $resource);
        $this->transformLinks($data, $resource);
        $this->transformLinkedResources($data, $resource);

        return $data;
    }

    protected function transformLinks(&$array, Resource $resource)
    {
        if ($self = $resource->getSelfLink()) {
            $array['_links']['self'] = $self->toArray();
        }

        foreach ($resource->getAllLinks() as $rel => $links) {
            if (!is_array($links)) {
                $array['_links'][$rel] = $links->toArray();
            } else {
                foreach ($links as $link) {
                    $array['_links'][$rel][] = $link->toArray();
                }
            }
        }
    }

    protected function transformLinkedResources(&$array, Resource $resource)
    {
       foreach ($resource->getAllLinkedResources() as $rel => $links) {
            if (!is_array($links)) {
                $array['_links'][$rel] = $links->getSelfLink()->toArray();
            } else {
                foreach ($links as $link) {
                    $array['_links'][$rel][] = $link->getSelfLink()->toArray();
                }
            }
        }
    }

    protected function transformResources(&$array, Resource $resource)
    {
        $embedded = [];
        foreach ($resource->getAllResources() as $rel => $resources) {
            if ($resources instanceof Resource) {
                if (!$resources->getUri()) {
                    $array[$rel] = $this->resourceToArray($resources);
                } else {
                    $embedded[$rel] = $this->resourceToArray($resources);
                }

                continue;
            }

            foreach ($resources as $resource) {
                if (!$resource->getUri()) {
                    $array[$rel][] = $this->resourceToArray($resource);
                } else {
                    $embedded[$rel][] = $this->resourceToArray($resource);
                }
            }
        }

        if ($embedded) {
            $array['_embedded'] = $embedded;
        }
    }
}
