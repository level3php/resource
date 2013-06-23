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
use Level3\Hal\LinkBuilder;
use Mockery as m;

class LinkBuilderTest extends TestCase
{
    public function testWithResource()
    {
        $mapper = m::mock('Level3\RepositoryMapper');
        $mapper->shouldReceive('getURI')
            ->once()
            ->with('foo', 'get', array('id' => 'bar'));

        $linkBuilder = new LinkBuilder($mapper);
        $this->assertSame($linkBuilder, $linkBuilder->withResource('foo', 'bar'));
    }

    public function testBoot()
    {
        $mapper = m::mock('Level3\RepositoryMapper');
        $mapper->shouldReceive('getURI')->andReturn('/foo');

        $linkBuilder = new LinkBuilder($mapper);
        $linkBuilder->withResource('foo', 'bar');

        $link = $linkBuilder->build();

        $this->assertSame('/foo', $link->getHref());    
    }  
}