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

class XmlFormatterTest extends FormatterTest {
    protected $class = 'Level3\Hal\Formatter\XmlFormatter';
    protected $nonPretty = 'HalFormatXMLNonPretty.xml';
    protected $pretty = 'HalFormatXMLPretty.xml';

    public function testConstructor()
    {

        $formatter = new $this->class();
        $resource = new Resource('foo', array('qux' => 'bar'));

        $link = new Link('foo');
        $link->setName('name');
        $link->setLang('lang');
        $link->setTitle('title');
        $link->isTemplated(true);

        $resource->addLink('quz', $link);
        $resource->addResource('baz',
            new Resource('foo', array('bar' => 'qux'))
        );

        $resource->setFormatter($formatter);

        $this->assertSame(
            $this->readResource($this->nonPretty),
            $resource->format()
        );


        $this->assertSame(
                $this->readResource($this->pretty),
                $resource->formatPretty()
        );

    }
}