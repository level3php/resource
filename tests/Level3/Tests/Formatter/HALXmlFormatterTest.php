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

class HALXmlFormatterTest extends FormatterTest
{
    protected $class = 'Level3\Formatter\HAL\XmlFormatter';
    protected $toNonPretty = 'ToFormatHALXMLNonPretty.xml';
    protected $toPretty = 'ToFormatHALXMLPretty.xml';
    protected $from = 'FromFormatHALXML.xml';
}
