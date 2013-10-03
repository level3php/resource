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

use Level3\Tests\TestCase;
use Level3\Resource;
use Level3\Resource\Link;
use Level3\Resource\Parameters;

abstract class FormatterTest extends TestCase {
    const EXAMPLE_URI = '/test';

    public function testFromRequest()
    {
        $formatter = new $this->class();
        $array = $formatter->fromRequest($this->readResource($this->from));

        $this->assertCount(1, $array);
        $this->assertSame('bar', $array['qux']);
    }

    /**
      * @expectedException Level3\Exceptions\BadRequest
      */
    public function testFromRequestInvalid()
    {
        $formatter = new $this->class();
        $array = $formatter->fromRequest('foo');
    }

    public function testToResponse()
    {
        $formatter = new $this->class();

        $repository = $this->createRepositoryMock();
        $resource = $this->createResource(array('qux' => 'bar'), self::EXAMPLE_URI);
        $resource->setData(array('qux' => 'bar'));

        $link = new Link('foo');
        $link->setName('name');
        $link->setLang('lang');
        $link->setTitle('title');
        $link->isTemplated(true);

        $resource->addLink('quz', $link);
        $resource->addResource('baz', 
            $this->createResource(array('bar' => 'qux'), self::EXAMPLE_URI)->setData(array('bar' => 'qux'))
        );


        $this->assertSame(
            $this->readResource($this->toNonPretty),
            $formatter->toResponse($resource)
        );


        if (
            version_compare(PHP_VERSION, '5.4' , '>=') || 
            $this->class != 'Level3\Formatter\JsonFormatter'
        ) {
            $this->assertSame(
                $this->readResource($this->toPretty),
                $formatter->toResponse($resource, true)
            );
        } else {
            $this->assertSame(
                $this->readResource($this->toNonPretty),
                $formatter->toResponse($resource, true)
            );
        }
    }

    protected function createResource($parameters, $uri)
    {
        $repository = $this->createRepositoryMock();
        $resource = new Resource($repository);

        $resource->setAttributes(new Parameters($parameters));
        
        $this->shouldReceiveGetResouceURI($repository, $resource, $uri);

        return $resource;
    }

    protected function shouldReceiveGetResouceURI($repository, Resource $resource, $uri)
    {  
        $repository->shouldReceive('getResourceURI')
            ->with($resource, Resource::DEFAULT_INTERFACE_METHOD)
            ->twice()
            ->andReturn($uri);
    }

    public function readResource($filename)
    {
        return file_get_contents(__DIR__ . '/../../Resources/' . $filename);
    }
}