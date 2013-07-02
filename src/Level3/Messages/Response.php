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

class Response
{
    protected $resource;
    protected $status;
    protected $headers = array();

    public function __construct(Resource $resource = null, $status = StatusCode::OK)
    {
        $this->setStatus($status);
        if ($resource) $this->setResource($resource);
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function addHeader($header, $value)
    {   
        $this->headers[$header] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getContent()
    {
        if ($this->resource === null) {
            return '';
        }

        return $this->resource->format();
    }
}
