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
        $result = $this->processor->find($level3Request);

        return $this->getResponse($result);
    }

    public function get(Request $request, $id = null)
    {
        $level3Request = $this->createLevel3Request($request, $id);
        $result = $this->processor->get($level3Request);

        return $this->getResponse($result);
    }

    public function post(Request $request, $id)
    {
        $level3Request = $this->createLevel3Request($request, $id);
        $result = $this->processor->post($level3Request);

        return $this->getResponse($result);
    }

    public function put(Request $request)
    {
        $level3Request = $this->createLevel3Request($request);
        $result = $this->processor->put($level3Request);

        return $this->getResponse($result);
    }

    public function delete(Request $request, $id)
    {
        $level3Request = $this->createLevel3Request($request, $id);
        $result = $this->processor->delete($level3Request);

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

    protected function createLevel3Request(Request $request, $id = null)
    {
        $key = $this->getResourceKey($request);
        $requestAttributes = $request->request->all();
        $content = $request->getContent();
        $requestHeaders = $this->getRequestHeaders($request);
        $pathInfo = $request->getPathInfo();
        $level3Request = $this->requestFactory->clear()
            ->withPathInfo($pathInfo)
            ->withKey($key)
            ->witId($id)
            ->withAttributes($requestAttributes)
            ->withHeaders($requestHeaders)
            ->withContent($content)
            ->create();
        return $level3Request;
    }

    protected function getRequestHeaders(Request $request)
    {
        $headers = $request->headers;
        $headerNames = $headers->keys();
        $result = array();
        foreach ($headerNames as $headerName) {
            $result[strtolower($headerName)] = $headers->get($headerName);
        }
    }

    protected function getResourceKey(Request $request)
    {
        $params = $request->attributes->all();

        $route = explode(':', $params['_route']);
        return $route[0];
    }
}
