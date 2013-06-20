<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Mappers;
use Silex\Application;
use Level3\MapperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Silex implements MapperInterface
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function mapRootTo($rootURI) {
        $app = $this->app;

        $app->get('/', function() use($app, $rootURI) {
            return $app->redirect($rootURI);
        });
    }

    public function mapFind($uri, $alias)
    {
        $this->app->get($uri, 'level3.controller:find')->bind($alias);
    }

    public function mapGet($uri, $alias)
    {
        $this->app->get($uri, 'level3.controller:get')->bind($alias);
    }
    
    public function mapPost($uri, $alias)
    {
        $this->app->post($uri, 'level3.controller:post')->bind($alias);
    }
    
    public function mapPut($uri, $alias)
    {
        $this->app->put($uri, 'level3.controller:put')->bind($alias);
    }
    
    public function mapDelete($uri, $alias)
    {
        $this->app->delete($uri, 'level3.controller:delete')->bind($alias);
    }

    public function getURI($alias, array $parameters = null)
    {
        try {
            return $this->app['url_generator']->generate($alias, $parameters);
        } catch (\Exception $e) {
            return null;
        }
    }
}