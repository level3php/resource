<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Controllers;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Level3\ResourceAccesor;
use Level3\Response as Level3Response;

class Silex
{
    private $app;
    private $accesor;

    public function __construct(Application $app, ResourceAccesor $accesor)
    {
        $this->app = $app;
        $this->accesor = $accesor;
    }

    public function get(Request $request, $id = null)
    {
        $key = $this->getResourceKey($request);
        $result = $this->accesor->get($key, $id);

        return $this->getResponse($result);
    }

    public function post(Request $request, $id)
    {
        $key = $this->getResourceKey($request);
        $result = $this->accesor->get($key, $id, $request->request->all());

        return $this->getResponse($result);
    }

    public function put(Request $request)
    {
        $key = $this->getResourceKey($request);
        $result = $this->accesor->put($key, $request->request->all());

        return $this->getResponse($result);
    }

    public function delete(Request $request, $id)
    {
        $key = $this->getResourceKey($request);
        $result = $this->accesor->delete($key, $id);

        return $this->getResponse($result);
    }

    protected function getResponse(Level3Response $response)
    {
        return new Response(
            $response->getContent(), 
            $response->getStatus(),
            $response->getHeaders()
        );
    }

    protected function getResourceKey(Request $request)
    {
        $params = $request->attributes->all();

        $route = explode(':', $params['_route']);
        return $route[0];
    }
}
