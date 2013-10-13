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

use Level3\Resource;
use Level3\Formatter;
use Level3\Exceptions\HTTPException;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Teapot\StatusCode;
use Exception;
use DateTime;
use DateInterval;

class Response extends SymfonyResponse
{
    protected $resource;
    protected $formatter;

    static public function createFromResource(Request $request, Resource $resource)
    {
        $response = new static();
        $response->setStatusCode(StatusCode::OK);
        $response->setResource($resource);
        $response->setFormatter($request->getFormatter());

        if ($cache = $resource->getCache()) {
            $date = new DateTime();
            $date->add(new DateInterval(sprintf('PT%dS', $cache)));

            $response->setExpires($date);
            $response->setTTL($cache);
        }

        if ($id = $resource->getId()) {
            $response->setEtag($id);
        }
   
        if ($date = $resource->getLastUpdate()) {
            $response->setLastModified($date);
        }     

        return $response;
    }

    static public function createFromException(Request $request, Exception $exception)
    {
        $code = StatusCode::INTERNAL_SERVER_ERROR;
        if ($exception instanceOf HTTPException) {
            $code = $exception->getCode();
        }

        $exceptionClass = explode('\\', get_class($exception));
        $resource = new Resource();
        $resource->setData(array(
            'type' => end($exceptionClass),
            'message' => $exception->getMessage()
        ));

        $response = static::createFromResource($request, $resource);
        $response->setStatusCode($code);

        return $response;
    }

    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
        $this->setContentType($formatter->getContentType());
    }

    public function getFormatter()
    {
        return $this->formatter;
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
        if (!$this->formatter instanceOf Formatter || !$this->resource) {
            return '';
        }

        return $this->formatter->toResponse($this->resource);
    }

    public function sendContent()
    {
        echo $this->getContent();

        return $this;
    }

    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type', $contentType);
    }
}
