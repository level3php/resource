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
use Level3\Resources\Resources;

class ResourcesTest extends TestCase {
    public function testSetMapper()
    {
        $hub = $this->getHub();
        $hub['resources'] = $hub->share(function() {
            return new Resources();
        });

        $hub->boot();

        $r = $hub['resources']->find();
        var_dump((string)$r);
    }
}