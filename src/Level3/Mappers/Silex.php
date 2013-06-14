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

class Silex implements MapperInterface
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function mapList($uri, $alias)
    {
        $this->app->get($uri, 'controller.resourcehub:get')->bind($alias);
    }

    public function mapGet($uri, $alias)
    {
        $this->app->get($uri, 'controller.resourcehub:get')->bind($alias);
    }
    
    public function mapPost($uri, $alias)
    {
        $this->app->post($uri, 'controller.resourcehub:post')->bind($alias);
    }
    
    public function mapPut($uri, $alias)
    {
        $this->app->put($uri, 'controller.resourcehub:put')->bind($alias);
    }
    
    public function mapDelete($uri, $alias)
    {
        $this->app->delete($uri, 'controller.resourcehub:delete')->bind($alias);
    }

    public function getURI($alias, array $parameters = null)
    {
        $this->app['url_generator']->generate($route, $parameters);

    }
}