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

        if ($pretty) {
            $options = $options | JSON_PRETTY_PRINT;
        }

        return json_encode($this->resourceToArray($resource), $options);
    }

    protected function resourceToArray(Resource $resource)
    {
        $data = [];

        $data['properties'] = $resource->getData();
        if ($self = $resource->getSelfLink()) {
            $data['links'][] = [
                'rel' => 'self',
                'href' => $self->getHref()
            ];
        }

        foreach ($resource->getAllLinks() as $rel => $links) {
            foreach ($links as $link) {
                $data['links'][] = [
                    'rel' => $rel,
                    'href' => $link->getHref()
                ];
            }
        }

        foreach ($resource->getAllResources() as $rel => $embeddedResources) {
            foreach ($embeddedResources as $embeddedResource) {
                if ($link = $embeddedResource->getSelfLink()) {
                    $embeddedLinks[$link->getHref()] = true;
                }
                
                $data['entities'][] = array_merge([
                    'rel' => $rel,
                ], $this->resourceToArray($embeddedResource));
            }
        }

        foreach ($resource->getAllLinkedResources() as $rel => $linkedResources) {
            foreach ($linkedResources as $linked) {
                $link = $linked->getSelfLink()->getHref();
                if (isset($embeddedLinks[$link])) {
                    continue;
                }

                $data['entities'][] = [
                    'rel' => $rel,
                    'href' => $link
                ];
            }
        }



        return $data;

    }
}
