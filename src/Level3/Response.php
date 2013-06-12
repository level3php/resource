<?php
namespace Level3;
use Teapot\StatusCode;

class Response
{
    const AS_JSON = 10;
    const AS_XML = 20;

    protected $hal;
    protected $format;
    protected $status;
    protected $headers = array();

    protected $content;

    public function __construct(Hal $hal = null, $status = StatusCode::OK)
    {
        $this->setStatus($status);
        $this->setHAL($hal);
    }

    public function setHAL(Hal $hal)
    {
        $this->hal = $hal;
        return $this->update();
    }

    public function getHAL()
    {
        return $this->hal;
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

    private function setContent($content = null)
    {
        $this->content = $content;
    }

    protected function update()
    {
        $content = null;
        switch ($this->format) {
            case null:
            case self::AS_JSON:
                $mime = 'application/hal+json';
                if ($this->data) $content = $this->data->asJSON();
                break;
            case self::AS_XML:
                $mime = 'application/hal+xml';
                if ($this->data) $content = $this->data->asXML();
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
