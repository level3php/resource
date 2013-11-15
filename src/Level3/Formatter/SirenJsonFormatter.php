<?php

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Resource\Link;
use Level3\Exceptions\BadRequest;

class SirenJsonFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/vnd.siren+json';

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
        $data = [];

        $this->transformDataAndMetadata($data, $resource);
        $this->transformResources($data, $resource);
        $this->transformLinks($data, $resource);
        $this->transformLinkedResources($data, $resource);

        return $data;
    }

    protected function transformDataAndMetadata(&$array, Resource $resource)
    {
        $array['class'] = null;
        if ($key = $resource->getRepositoryKey()) {
            $array['class'] = explode('/', $key);
        }
        
        if ($title = $resource->getTitle()) {
            $array['title'] = $title;
        }

        $array['properties'] = $resource->getData();
    }

    protected function transformLinks(&$array, Resource $resource)
    {
        if ($self = $resource->getSelfLink()) {
            $array['links'][] = [
                'rel' => 'self',
                'href' => $self->getHref()
            ];
        }

        foreach ($resource->getAllLinks() as $rel => $links) {
            foreach ($links as $link) {
                $array['links'][] = [
                    'rel' => $rel,
                    'href' => $link->getHref()
                ];
            }
        } 
    }

    protected function transformLinkedResources(&$array, Resource $resource)
    {
        $embeddedLinks = [];
        if (isset($array['entities'])) {
            foreach ($array['entities'] as $entity) {
                if (isset($entity['href'])) {
                    $embeddedLinks[$entity['href']] = true;
                }
            }
        }

        foreach ($resource->getAllLinkedResources() as $rel => $linkedResources) {
            if (!is_array($linkedResources)) {
                $linkedResources = [$linkedResources];
            }

            foreach ($linkedResources as $linked) {
                $link = $linked->getSelfLink()->getHref();
                if (isset($embeddedLinks[$link])) {
                    continue;
                }

                $array['entities'][] = [
                    'rel' => $rel,
                    'href' => $link
                ];
            }
        }
    }

    protected function transformResources(&$array, Resource $resource)
    {
        foreach ($resource->getAllResources() as $rel => $resources) {
            if (!is_array($resources)) {
                $resources = [$resources];
            }

            foreach ($resources as $resource) {
                $array['entities'][] = $this->doTransformResource($array, $rel, $resource);
            }
        }
    }

    protected function doTransformResource(Array &$array, $rel, Resource $resource)
    {
        $data = $this->resourceToArray($resource);
        if (!$data['class']) {
            $data['class'] = array_merge($array['class'], [$rel]);
        }

        $metadata = [];
        $metadata['rel'] = $rel;

        if ($resource->getUri()) {
            $metadata['href'] = $resource->getSelfLink()->getHref();
        }

        return array_merge($metadata, $data);
    }
}
