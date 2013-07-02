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

    public function __construct(RequestProcessor $processor, AuthenticationMethod $method, ResponseFactory $responseFactory)
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

    protected function authenticateRequest(Request $request)
    {
        if (!$this->hasAuthorizationHeader($request)) {
            throw new MissingApiKey();
        }

        $apiKey = $this->getApiKeyFromRequest($request);

        $user = $this->userRepository->findByApiKey($apiKey);
        $this->verifySignature($request, $user->getSecretKey());
        $request->setUser($user);

        return $request;
    }

    protected function hasAuthorizationHeader(Request $request)
    {
        return $request->hasHeader(self::AUTHORIZATION_HEADER);
    }

    protected function hasSignatureHeader(Request $request)
    {
        return $request->hasHeader(self::SIGNATURE_HEADER);
    }

    protected function getApiKeyFromRequest(Request $request)
    {
        $authContent = $this->extractAuthContent($request);
        return explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $authContent)[0];
    }

    protected function createForbiddenResponse()
    {
        return $this->responseFactory->create(null, StatusCode::FORBIDDEN);
    }

    protected function verifySignature(Request $request, $privateKey)
    {
        $originalContent = $request->getContent();
        $calculatedSignature = hash_hmac(self::HASH_ALGORITHM, $originalContent, $privateKey);

        $authContent = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $this->extractAuthContent($request));

        $signature = $authContent[1];

        if ($calculatedSignature !== $signature) {
            throw new InvalidSignature();
        }
    }

    protected function extractAuthContent(Request $request)
    {
        $authHeader = $request->getHeader(self::AUTHORIZATION_HEADER);

        if (explode(self::TOKEN_SEPARATOR, $authHeader)[0] !== self::TOKEN) {
            throw new MissingApiKey();
        }

        return explode(self::TOKEN_SEPARATOR, $authHeader)[1];
    }
}
