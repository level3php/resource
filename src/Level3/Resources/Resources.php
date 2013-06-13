<?php

namespace Level3\Resources;
use Level3\ResourceManager as ResourceManager;
use Level3\ResourceManager\DeleteInterface;
use Level3\ResourceManager\GetInterface;
use Level3\ResourceManager\PostInterface;
use Level3\ResourceManager\PutInterface;

/**
* Available resources at this API
*/
class Resources extends ResourceManager implements GetInterface
{
    public function getOne($id)
    {
        $hub = $this->getHub();
        $resourceManager = $hub[$id];

        $data = array();
        $data['name'] = get_class($resourceManager);
        $data['description'] = $resourceManager->getDescription();

        $resource = $this->create($id);
        $resource->setData($data);

        return $resource;
    }
    
    public function get()
    {
        $resources = array();
        foreach($this->getHub()->keys() as $id) {
            $resources = $this->getOne($id);
        }

        var_dump($resources);
    }
}