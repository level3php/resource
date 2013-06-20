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
use Level3\ResourceRepository\Finder;
use Level3\ResourceRepository\Getter;
use Level3\ResourceRepository\Deleter;
use Level3\ResourceRepository\Poster;
use Level3\ResourceRepository\Putter;

/**
* Foo
*/
class DummyResourceRepository
    extends ResourceRepository
    implements Finder, Getter, Poster, Putter, Deleter
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
        return array('foo' => 'bar');
    }
}