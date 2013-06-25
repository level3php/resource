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
    const AS_JSON = 10;
    const AS_XML = 20;

    protected $resource;
    protected $format;
    protected $status;
    protected $headers = array();

    protected $content;

    public function __construct(Resource $resource = null, $status = StatusCode::OK)
    {
        $this->setStatus($status);
        if ($resource) $this->setResource($resource);
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
        return $this->update();
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

    public function setFormat($format = self::AS_JSON)
    {
        $this->format = $format;
        return $this->update();
    }

    public function getFormat()
    {
        return $this->format;
    }

    private function setContent($content = null)
    {
        $this->content = $content;
    }

    public function getContent($content = null)
    {
        return $this->content;
    }

    protected function update()
    {
        $content = null;
        switch ($this->format) {
            case null:
            case self::AS_JSON:
                $mime = 'application/hal+json';
                if ($this->resource) $content = $this->resource->asJson(true);
                break;
            case self::AS_XML:
                $mime = 'application/hal+xml';
                if ($this->resource) $content = $this->resource->asXML();
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Invalid format given "%d"', $this->format
                ));
                break;
        }

        $this->addHeader('Content-Type', $mime);
        return $this->setContent($content);
    }
}
