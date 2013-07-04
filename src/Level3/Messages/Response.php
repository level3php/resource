<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Messages;

use Level3\Hal\Resource;
use Teapot\StatusCode;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    protected $resource;

    public function __construct(Resource $resource = null, $status = StatusCode::OK, $headers = array())
    {
        parent::__construct('', $status, $headers);
        $this->resource = $resource;
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function addHeader($header, $value)
    {   
        $this->headers->set($header, $value, false);
    }

    public function setHeader($header, $value)
    {
        $this->headers->set($header, $value, true);
    }

    public function getHeaders($header)
    {
        return $this->headers->get($header, null, false);
    }

    public function getHeader($header)
    {
        return $this->headers->get($header);
    }

    public function getContent()
    {
        if ($this->resource === null) {
            return '';
        }

        return $this->resource->format();
    }
}