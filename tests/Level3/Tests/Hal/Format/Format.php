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
use Level3\Tests\TestCase;
use Level3\Hal\Resource;
use Level3\Hal\Link;
use Level3\Hal\Format\JSON;

abstract class Format extends TestCase {
    public function testConstructor()
    {
        $format = new $this->class();
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

        $this->assertSame(
            $this->readResource($this->nonPretty), 
            $format->to($resource, false)
        );

        $this->assertSame(
            $this->readResource($this->pretty), 
            $format->to($resource, true)
        );
    }

    public function readResource($filename)
    {
        return file_get_contents(__DIR__ . '/../../../Resources/' . $filename);
    }
}