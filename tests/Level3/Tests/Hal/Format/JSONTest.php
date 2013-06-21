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
use Level3\Hal\Format\JSON;

class JSONTest extends Format {
    protected $class = 'Level3\Hal\Format\JSON';
    protected $nonPretty = 'e08551c0703353afbc692c15851bc8fa';
    protected $pretty = '119503c82d5442e421d7a18654756070';
}