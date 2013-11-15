<?php

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Exceptions\BadRequest;

class HALJsonFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/hal+json';

    public function fromRequest($string)
    {
        if (strlen($string) == 0) {
            return [];
        }

        $array = json_decode($string, true);

        if (!is_array($array)) {
            throw new BadRequest();
        }

        return $array;
    }

    public function toResponse(Resource $resource, $pretty = false)
    {
        $options = JSON_UNESCAPED_SLASHES;

        if (!$pretty) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        return json_encode($this->resourceToArray($resource), $options);
    }

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
            if (count($links) == 1) {
                $array['_links'][$rel] = end($links)->toArray();
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
            if (count($links) == 1) {
                $array['_links'][$rel] = end($links)->getSelfLink()->toArray();
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
            if ($resources instanceOf Resource) {
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
