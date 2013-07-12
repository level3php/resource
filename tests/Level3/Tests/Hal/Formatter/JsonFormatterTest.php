<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Hal\Formatter;

class JsonFormatterTest extends FormatterTest {
    protected $class = 'Level3\Hal\Formatter\JsonFormatter';
    protected $nonPretty = 'HalFormatJSONNonPretty.json';
    protected $pretty = 'HalFormatJSONPretty.json';
}