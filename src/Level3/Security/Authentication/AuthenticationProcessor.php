<?php

namespace Level3\Security\Authentication;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Level3\Security\Authentication\Exceptions\InvalidCredentials;
use Level3\Security\Authentication\Exceptions\MissingCredentials;
use Level3\Security\Authentication\Exceptions\BadCredentials;
use Teapot\StatusCode;

class AuthenticationProcessor implements RequestProcessor
{
    private $processor;
    private $method;
    private $responseFactory;

    public function __construct(RequestProcessor $processor, Method $method, ResponseFactory $responseFactory)
    {
        $this->processor = $processor;
        $this->method = $method;
        $this->responseFactory = $responseFactory;
    }
    
    public function find(Request $request)
    {
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (BadCredentials $e) {
            return $this->createForbiddenResponse();
        } catch (MissingCredentials $e) {
            return $this->processor->find($request);
        } catch (InvalidCredentials $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->find($authenticatedRequest);
    }

    public function get(Request $request){
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (BadCredentials $e) {
            return $this->createForbiddenResponse();
        } catch (MissingCredentials $e) {
            return $this->processor->get($request);
        } catch (InvalidCredentials $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->get($authenticatedRequest);
    }

    public function post(Request $request){
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (BadCredentials $e) {
            return $this->createForbiddenResponse();
        } catch (MissingCredentials $e) {
            return $this->processor->post($request);
        } catch (InvalidCredentials $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->post($authenticatedRequest);
    }

    public function put(Request $request){
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (BadCredentials $e) {
            return $this->createForbiddenResponse();
        } catch (MissingCredentials $e) {
            return $this->processor->put($request);
        } catch (InvalidCredentials $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->put($authenticatedRequest);
    }

    public function delete(Request $request){
        try {
            $authenticatedRequest = $this->method->authenticateRequest($request);
        } catch (BadCredentials $e) {
            return $this->createForbiddenResponse();
        } catch (MissingCredentials $e) {
            return $this->processor->delete($request);
        } catch (InvalidCredentials $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->delete($authenticatedRequest);
    }

    protected function createForbiddenResponse()
    {
        return $this->responseFactory->create(null, StatusCode::FORBIDDEN);
    }
}