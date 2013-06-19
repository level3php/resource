<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Mocks;
use Level3\ResourceRepository;
use Level3\ResourceRepository\DeleteInterface;
use Level3\ResourceRepository\GetInterface;
use Level3\ResourceRepository\FindInterface;
use Level3\ResourceRepository\PostInterface;
use Level3\ResourceRepository\PutInterface;

/**
* Foo
*/
class DummyResourceRepository
    extends ResourceRepository
    implements FindInterface, GetInterface, PostInterface, PutInterface, DeleteInterface
{
    public function get($id)
    {

    }
    
    public function find()
    {

    }

    public function delete($id)
    {

    }
    
    public function put($data)
    {

    }

    public function post($id, $data)
    {

    }
    
    protected function resource($id)
    {

    }

}