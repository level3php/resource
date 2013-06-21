<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Hal\Format;
use Level3\Hal\Resource;
use Level3\Hal\Link;
use Level3\Hal\Format\XML;

class XMLTest extends Format {
    protected $class = 'Level3\Hal\Format\XML';
    protected $nonPretty = '440d128f007be4f9d051f88e2635e68a';
    protected $pretty = '740d8ff96a2bb8f65e85e4795c800268';
}