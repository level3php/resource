<?php

namespace Level3\Messages;

use Level3\Security\Authentication\Credentials;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestFactory
{
    private $key;
    private $id;
    private $credentials;
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

    public function withCredentials(Credentials $credentials)
    {
        $this->credentials = $credentials;
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

        if ($this->credentials !== null) {
            $request->setCredentials($this->credentials);
        }

        return $request;
    }

    public function clear()
    {
        $this->key = null;
        $this->id = null;
        $this->credentials = null;
        $this->symfonyRequest = null;
        return $this;
    }
}