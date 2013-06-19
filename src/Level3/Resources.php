<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3;
use Level3\ResourceRepository\Finder;

/**
 * Available resources at this API
 */
class Resources extends ResourceRepository implements Finder
{
    protected function resource($id)
    {
        $hub = $this->getHub();
        $resourceManager = $hub[$id];

        $class = explode('\\', get_class($resourceManager));

        $data = array();
        $data['name'] = end($class);
        $data['description'] = $resourceManager->getDescription();

        return $data;
    }
    
    public function find()
    {
        $result = array();
        foreach($this->getHub()->keys() as $id) {
            $result[] = $this->create($id);
        }

        return $result;
    }
}