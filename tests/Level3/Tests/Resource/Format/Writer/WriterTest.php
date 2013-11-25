<?php

namespace Level3\Tests\Resource\Format\Writer;

use Level3\Tests\TestCase;
use Level3\Resource\Resource;
use Level3\Resource\Link;

abstract class WriterTest extends TestCase
{
    const EXAMPLE_URI = '/test';

    public function testWrite()
    {
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
            ]
        ]);

        $link = new Link('foo');
        $link->setName('name');
        $link->setLang('lang');
        $link->setTitle('title');
        $link->setTemplated(true);

        $resource->setLink('quz', $link);

        $resource->setLinks('foo', [
            $link,
            new Link('qux')
        ]);

        $subResource = $this->createResource(self::EXAMPLE_URI)->setData(['value' => 'qux']);
        $subResource->addResource(
            'foo',
            $this->createResource(self::EXAMPLE_URI)->setData(['foo' => 'qux'])
        );

        $subResource->addResource(
            'baz',
            $this->createResource()->setData(['foo' => 'qux'])
        );

        $resource->addResources('baz', [
            $subResource,
            $this->createResource()->setData(['baz' => 'foo'])
        ]);

        $subResource->linkResource(
            'qux',
            $this->createResource(self::EXAMPLE_URI)->setData([])->setTitle('qux')
        );

        $subResource->linkResources('foo', [
            $this->createResource(self::EXAMPLE_URI)->setData([]),
            $this->createResource(self::EXAMPLE_URI)->setData([])
        ]);

        $formatter = new $this->class(true);
        $this->assertSame(
            $this->readResource($this->toPretty),
            trim($formatter->execute($resource, true))
        );

        $formatter = new $this->class(false);
        $this->assertSame(
            $this->readResource($this->toNonPretty),
            trim($formatter->execute($resource))
        );
    }

    public function testGetContentType()
    {
        $writer = new $this->class();
        $this->assertSame($this->mime, $writer->getContentType());
    }

    protected function createResource($uri = null)
    {
        $resource = new Resource();
        if ($uri) {
            $resource->setURI($uri);
        }

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
        return trim(file_get_contents(__DIR__ . '/../../../../Resources/' . $filename));
    }
}
