<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Resources;
use Level3\ResourceManager;
use Level3\ResourceManager\FindInterface;

/**
 * Available resources at this API
 */
class Resources extends ResourceManager implements FindInterface
{
    protected function resource($id)
    {
        $hub = $this->getHub();
        $resourceManager = $hub[$id];

        $data = array();
        $data['name'] = get_class($resourceManager);
        $data['description'] = $resourceManager->getDescription();

        return $data;
    }
    
    public function find()
    {
        $resources = array();
        foreach($this->getHub()->keys() as $id) {
            $resources = $this->create($id);
        }

        var_dump($resources);
    }
}