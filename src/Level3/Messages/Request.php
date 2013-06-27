<?php

namespace Level3\Messages;

use Level3\Messages\Exceptions\AttributeNotFound;
use Level3\Messages\Exceptions\HeaderNotFound;
use Level3\Security\Authentication\AuthenticatedUser;
use Level3\Security\Authentication\User;

class Request
{
    private $user;
    private $pathInfo;
    private $id;
    private $key;
    private $attributes;
    private $headers;
    private $content;

    public function __construct($pathInfo, $key, array $headers, array $attributes, $content)
    {
        $this->pathInfo = $pathInfo;
        $this->key = $key;
        $this->user = new User();
        $this->headers = $headers;
        $this->attributes = $attributes;
        $this->content = $content;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(AuthenticatedUser $user)
    {
        $this->user = $user;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($headerName)
    {
        if (!isset($this->headers[$headerName])) {
            throw new HeaderNotFound($headerName);
        }
        return $this->headers[$headerName];
    }

    public function hasHeader($headerName)
    {
        return isset($this->headers[$headerName]);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    public function getAttribute($key)
    {
        if (!$this->hasAttribute($key)) {
            throw new AttributeNotFound($key);
        }

        return $this->attributes[$key];
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPathInfo()
    {
        return $this->pathInfo;
    }
}