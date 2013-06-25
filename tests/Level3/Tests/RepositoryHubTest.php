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
use Level3\RepositoryHub;
use Mockery as m;

class RepositoryHubTest extends TestCase
{
    public function testRegisterDefinition()
    {
        $repository = m::mock('Level3\Repository');
        $repository->shouldReceive('setKey')->once()->andReturn(array('foo'));

        $hub = new RepositoryHub();
        $hub->registerDefinition('foo', function() use ($repository) {
            return $repository;
        });

        $this->assertSame($repository, $hub->get('foo'));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testRegisterDefinitionInvalidKey()
    {
        $hub = new RepositoryHub();
        $hub->registerDefinition('', function() {});
    }

    /**
     * @expectedException RuntimeException
     */
    public function testRegisterDefinitionInvalidClosureResult()
    {
        $hub = new RepositoryHub();
        $hub->registerDefinition('foo', function() {});
        $hub->get('foo');
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGetNotExistingDefinition()
    {
        $hub = new RepositoryHub();
        $hub->get('foo');
    }

    public function testGetKeys()
    {
        $hub = new RepositoryHub();
        $hub->registerDefinition('foo', function() {});

        $this->assertSame(array('foo'), $hub->getKeys());   
    }

    /**
     * @dataProvider isMethodsDataProiver
     */
    public function testIs($interface, $method, $assert)
    {
        $repository = m::mock('Level3\Repository,Level3\Repository\\' . $interface);
        $repository->shouldReceive('setKey')->once()->andReturn(array('foo'));

        $hub = new RepositoryHub();
        $hub->registerDefinition('foo', function() use ($repository) {
            return $repository;
        });

        $this->assertSame($assert, $hub->$method('foo'));
    }

    public function isMethodsDataProiver()
    {
        return array(
            array('Finder', 'isFinder', true),
            array('Getter', 'isGetter', true),
            array('Putter', 'isPutter', true),
            array('Poster', 'isPoster', true),
            array('Deleter', 'isDeleter', true),
            array('Getter', 'isFinder', false),
            array('Putter', 'isGetter', false),
            array('Poster', 'isPutter', false),
            array('Deleter', 'isPoster', false),
            array('Finder', 'isDeleter', false),
        );
    }
}
