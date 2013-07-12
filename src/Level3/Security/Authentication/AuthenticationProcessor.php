<?php

namespace Level3\Security\Authentication;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Security\Authentication\Exceptions\MissingCredentials;

class AuthenticationProcessor implements RequestProcessor
{
    private $processor;
    private $method;

    public function __construct(RequestProcessor $processor, AuthenticationMethod $method)
    {
        $this->processor = $processor;
        $this->method = $method;
    }

    public function find(Request $request)
    {
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (MissingCredentials $e) {
            return $this->processor->find($request);
        }

        return $this->processor->find($authenticatedRequest);
    }

    public function get(Request $request)
    {
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (MissingCredentials $e) {
            return $this->processor->get($request);
        }

        return $this->processor->get($authenticatedRequest);
    }

    public function post(Request $request)
    {
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (MissingCredentials $e) {
            return $this->processor->post($request);
        }

        return $this->processor->post($authenticatedRequest);
    }

    public function put(Request $request)
    {
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (MissingCredentials $e) {
            return $this->processor->put($request);
        }

        return $this->processor->put($authenticatedRequest);
    }

    public function delete(Request $request)
    {
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (MissingCredentials $e) {
            return $this->processor->delete($request);
        }

        return $this->processor->delete($authenticatedRequest);
    }
}
