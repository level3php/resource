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

abstract class FormatterTest extends TestCase
{
    const EXAMPLE_URI = '/test';

    public function testFromRequest()
    {
        $formatter = new $this->class();
        $array = $formatter->fromRequest($this->readResource($this->from));

        $this->assertCount(9, $array);
        $this->assertSame(1, (int) $array['bar']);
    }

    /**
      * @expectedException Level3\Exceptions\BadRequest
      */
    public function testFromRequestInvalid()
    {
        $formatter = new $this->class();
        $array = $formatter->fromRequest('foo');
    }

    public function testFromRequestEmpty()
    {
        $formatter = new $this->class();
        $this->assertSame([], $formatter->fromRequest(''));
    }

    public function testToResponse()
    {
        $formatter = new $this->class();

        $repository = $this->createRepositoryMock();
        $resource = $this->createResource(self::EXAMPLE_URI);
        $resource->setData([
            'value' => 'bar',
            'bar' => 1,
            'foo' => true,
            'array' => [
                'bar' => 'foo'
            ],
            'arrayOfarrays' => [
                ['bar' => 'foo'],
                ['foo' => 'bar']
            ],
            'arrayOfstrings' => [
                'foo', 'bar'
            ],
            '@atribute' => 'foo'
        ]);

        $link = new Link('foo');
        $link->setName('name');
        $link->setLang('lang');
        $link->setTitle('title');
        $link->isTemplated(true);

        $resource->setLink('quz', $link);

        $resource->setLinks('foo', [
            $link,
            new Link('qux')
        ]);

        $subResource = $this->createResource(self::EXAMPLE_URI)->setData(['value' => 'qux']);
        $subResource->addResource('foo',
            $this->createResource(self::EXAMPLE_URI)->setData(['foo' => 'qux'])
        );

        $resource->addResources('baz', [
            $subResource
        ]);

        $this->assertSame(
            $this->readResource($this->toPretty),
            trim($formatter->toResponse($resource, true))
        );

        $this->assertSame(
            $this->readResource($this->toNonPretty),
            trim($formatter->toResponse($resource))
        );
    }

    protected function createResource($uri)
    {
        $resource = new Resource();
        $resource->setURI($uri);

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
        return trim(file_get_contents(__DIR__ . '/../../Resources/' . $filename));
    }
}
