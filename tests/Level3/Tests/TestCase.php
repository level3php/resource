<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests;
use Level3\ResourceHub;
use Level3\Mocks\Mapper;
use Level3\Mocks\ResourceManager;

use Teapot\StatusCode;

class TestCase extends \PHPUnit_Framework_TestCase
{   
    protected function getHub()
    {
        $mapper = new Mapper;

        $hub = new ResourceHub();
        $hub->setMapper($mapper);

        return $hub;
    }
}
