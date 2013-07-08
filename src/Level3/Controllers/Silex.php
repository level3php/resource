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
use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\RequestFactory;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Level3\Response as Level3Response;

class Silex
{
    private $app;
    private $processor;
    private $requestFactory;

    public function __construct(Application $app, RequestProcessor $processor, RequestFactory $requestFactory)
    {
        $this->app = $app;
        $this->processor = $processor;
        $this->requestFactory = $requestFactory;
    }

    public function find(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        return $this->processor->find($level3Request);
    }

    public function get(Request $request, $id = null)
    {
        $level3Request = $this->createLevel3Request($request, $id);
        return $this->processor->get($level3Request);
    }

    public function post(Request $request, $id)
    {
        $level3Request = $this->createLevel3Request($request, $id);
        return $this->processor->post($level3Request);
    }

    public function put(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        return $this->processor->put($level3Request);
    }

    public function delete(Request $request, $id)
    {
        $level3Request = $this->createLevel3Request($request, $id);
        return $this->processor->delete($level3Request);
    }

    protected function createLevel3Request(Request $request, $id = null)
    {
        $key = $this->getResourceKey($request);
        $level3Request = $this->requestFactory->clear()
            ->withKey($key)
            ->withId($id)
            ->withSymfonyRequest($request)
            ->create();
        return $level3Request;
    }

    protected function getResourceKey(Request $request)
    {
        $params = $request->attributes->all();

        $route = explode(':', $params['_route']);
        return $route[0];
    }
}