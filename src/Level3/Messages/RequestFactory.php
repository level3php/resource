<?php

namespace Level3\Messages;

use Level3\Security\Authentication\User;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestFactory
{
    private $key;
    private $id;
    private $user;
    private $symfonyRequest;

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

    public function withSymfonyRequest(SymfonyRequest $symfonyRequest)
    {
        $this->symfonyRequest = $symfonyRequest;
        return $this;
    }

    public function create()
    {
        $request = new Request($this->key, $this->symfonyRequest);

        if ($this->id !== null) {
            $request->setId($this->id);
        }

        if ($this->user !== null) {
            $request->setCredentials($this->user);
        }

        return $request;
    }

    public function clear()
    {
        $this->key = null;
        $this->id = null;
        $this->user = null;
        $this->symfonyRequest = null;
        return $this;
    }
}