<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Hal\FormatTer;

use Level3\Tests\TestCase;
use Level3\Hal\Resource;
use Level3\Hal\Link;

abstract class FormatterTest extends TestCase {
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


        if (version_compare(PHP_VERSION, '5.4' >= 0)) {
            $this->assertSame(
                $this->readResource($this->pretty),
                $resource->formatPretty()
            );
        } else {
            $this->assertSame(
                $this->readResource($this->nonPretty),
                $resource->formatPretty()
            );
        }
    }

    public function readResource($filename)
    {
        return file_get_contents(__DIR__ . '/../../../Resources/' . $filename);
    }
}