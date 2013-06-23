<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Tests\Hal;
use Level3\Tests\TestCase;
use Level3\Hal\ResourceBuilder;
use Mockery as m;

class ResourceBuilderTest extends TestCase
{
    public function testWithURI()
    {
        $builder = new ResourceBuilder(m::mock('Level3\RepositoryMapper'));

        $expected = 'foo';
        $this->assertSame($builder, $builder->withURI($expected));

        $this->assertSame($expected, $builder->build()->getURI()); 
    }

    public function testWithData()
    {
        $builder = new ResourceBuilder(m::mock('Level3\RepositoryMapper'));

        $expected = array('foo');
        $this->assertSame($builder, $builder->withData($expected));

        $this->assertSame($expected, $builder->build()->getData()); 
    }

    public function WithEmbedded()
    {
        $builder = new ResourceBuilder(m::mock('Level3\RepositoryMapper'));

        $this->assertSame($builder, $builder->withEmbedded('next', 'mock', 2));
        
        $raw = $builder->build()->toArray();
        $this->assertCount(1, $raw['_embedded']['next']);
        $this->assertTrue(isset($raw['_embedded']['next'][0]['foo']));
    }

    public function WithRelation()
    {
        $builder = new ResourceBuilder(m::mock('Level3\RepositoryMapper'));

        $this->assertSame($builder, $builder->withRelation('next', 'mock', 2));

        $raw = $builder->build()->toArray();
        $this->assertCount(1, $raw['_links']['next']);
        $this->assertSame('/mock/2', $raw['_links']['next'][0]['href']);
    }

    public function WithLink()
    {
        $builder = new ResourceBuilder(m::mock('Level3\RepositoryMapper'));

        $this->assertSame($builder, $builder->withLink('search', 'mock', 'find', array()));
        
        $raw = $builder->build()->toArray();
        $this->assertCount(1, $raw['_links']['search']);
        $this->assertSame('/mock', $raw['_links']['search'][0]['href']);
    }
}