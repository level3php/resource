<?php

namespace Level3\Messages;

use Level3\Security\Authentication\User;

class RequestFactory
{
    private $key;
    private $id;
    private $user;
    private $attributes = array();
    private $headers = array();
    private $content;

    public function withKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function withId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function withUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function withAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function withContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function withHeaders(array $array)
    {
        $this->headers = $array;
        return $this;
    }

    public function create()
    {
        $request = new Request($this->key, $this->headers, $this->attributes, $this->content);

        if ($this->id !== null) {
            $request->setId($this->id);
        }

        if ($this->user !== null) {
            $request->setUser($this->user);
        }

        return $request;
    }

    public function clear()
    {
        $this->key = null;
        $this->id = null;
        $this->user = null;
        $this->attributes = array();
        $this->headers = array();
        $this->content = null;
        return $this;
    }
}