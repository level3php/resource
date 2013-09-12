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
        $parameters = $this->createParametersMock();
        
        $mapper = m::mock('Level3\RepositoryMapper');
        $mapper->shouldReceive('getURI')
            ->once()
            ->with('foo', 'get', $parameters);

        $linkBuilder = new LinkBuilder();
        $linkBuilder->setRepositoryMapper($mapper);
        $this->assertSame($linkBuilder, $linkBuilder->withResource('foo', $parameters));
    }

    public function testBoot()
    {
        $mapper = m::mock('Level3\RepositoryMapper');
        $mapper->shouldReceive('getURI')->andReturn('/foo');

        $linkBuilder = new LinkBuilder($mapper);
        $linkBuilder->setRepositoryMapper($mapper);
        $linkBuilder->withResource('foo', $this->createParametersMock());

        $link = $linkBuilder->build();

        $this->assertSame('/foo', $link->getHref());    
    }  
}