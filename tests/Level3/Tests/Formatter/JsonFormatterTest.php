<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Formatter;

class JsonFormatterTest extends FormatterTest {
    protected $class = 'Level3\Formatter\JsonFormatter';
    protected $toNonPretty = 'ToFormatJSONNonPretty.json';
    protected $toPretty = 'ToFormatJSONPretty.json';
    protected $from = 'FromFormatJSON.json';
}