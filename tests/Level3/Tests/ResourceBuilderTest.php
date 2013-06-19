<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests;
use Level3\ResourceBuilder;
use Level3\Mocks\DummyResourceRepository;

use Teapot\StatusCode;

class ResourceBuilderTest extends TestCase {
    protected function getHub()
    {
        $hub = parent::getHub();

        $hub['mock'] = $hub->share(function ($c) {
            return new DummyResourceRepository();
        });

        $hub->boot();
        return $hub; 
    }

    public function testWithKeyAndId()
    {
        $builder = new ResourceBuilder($this->getHub());

        $this->assertSame($builder, $builder->withKey('mock'));
        $this->assertSame($builder, $builder->withId(1));

        $links = $builder->build()->getLinks();
        $this->assertSame('/mock/1', $links['self']->getHref()); 
    }

    public function testWithEmbedded()
    {
        $builder = new ResourceBuilder($this->getHub());

        $this->assertSame($builder, $builder->withEmbedded('next', 'mock', 2));
        
        $raw = $builder->build()->toArray();
        $this->assertCount(1, $raw['_embedded']['next']);
        $this->assertTrue(isset($raw['_embedded']['next'][0]['foo']));
    }

    public function testWithRelation()
    {
        $builder = new ResourceBuilder($this->getHub());

        $this->assertSame($builder, $builder->withRelation('next', 'mock', 2));

        $raw = $builder->build()->toArray();
        $this->assertCount(1, $raw['_links']['next']);
        $this->assertSame('/mock/2', $raw['_links']['next'][0]['href']);
    }

    public function testWithLink()
    {
        $builder = new ResourceBuilder($this->getHub());

        $this->assertSame($builder, $builder->withLink('search', 'mock', 'find', array()));
        
        $raw = $builder->build()->toArray();
        $this->assertCount(1, $raw['_links']['search']);
        $this->assertSame('/mock', $raw['_links']['search'][0]['href']);
    }
}

