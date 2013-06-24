<?php

namespace Level3\Security\Authentication;

use Level3\Messages\Processors\RequestProcessor;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Level3\Security\Authentication\Exceptions\InvalidSignature;
use Level3\Security\Authentication\Exceptions\MissingApiKey;
use Level3\Security\Authentication\Exceptions\UserNotFound;
use Teapot\StatusCode;

class AuthenticationProcessor implements RequestProcessor
{
    const API_KEY_HEADER = 'X-Yunait-APIKey';
    const SIGNATURE_HEADER = 'X-Yunait-Signature';
    const HASH_ALGORITHM = 'sha256';

    private $processor;
    private $userRepository;
    private $responseFactory;

    public function __construct(RequestProcessor $processor, UserRepository $userRepository, ResponseFactory $responseFactory)
    {
        $this->processor = $processor;
        $this->userRepository = $userRepository;
        $this->responseFactory = $responseFactory;
    }
    public function find(Request $request)
    {
        try {
            $authenticatedRequest = $this->authenticateRequest($request);
        } catch (UserNotFound $e) {
            return $this->createForbiddenResponse();
        } catch (MissingApiKey $e) {
            return $this->processor->find($request);
        } catch (InvalidSignature $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->find($authenticatedRequest);
    }

    public function get(Request $request){
        try {
            $authenticatedRequest = $this->authenticateRequest($request);
        } catch (UserNotFound $e) {
            return $this->createForbiddenResponse();
        } catch (MissingApiKey $e) {
            return $this->processor->get($request);
        } catch (InvalidSignature $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->get($authenticatedRequest);
    }

    public function post(Request $request){
        try {
            $authenticatedRequest = $this->authenticateRequest($request);
        } catch (UserNotFound $e) {
            return $this->createForbiddenResponse();
        } catch (MissingApiKey $e) {
            return $this->processor->post($request);
        } catch (InvalidSignature $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->post($authenticatedRequest);
    }

    public function put(Request $request){
        try {
            $authenticatedRequest = $this->authenticateRequest($request);
        } catch (UserNotFound $e) {
            return $this->createForbiddenResponse();
        } catch (MissingApiKey $e) {
            return $this->processor->put($request);
        } catch (InvalidSignature $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->put($authenticatedRequest);
    }

    public function delete(Request $request){
        try {
            $authenticatedRequest = $this->authenticateRequest($request);
        } catch (UserNotFound $e) {
            return $this->createForbiddenResponse();
        } catch (MissingApiKey $e) {
            return $this->processor->delete($request);
        } catch (InvalidSignature $e) {
            return $this->createForbiddenResponse();
        }

        return $this->processor->delete($authenticatedRequest);
    }

    protected function authenticateRequest(Request $request)
    {
        if (!$this->hasApiKeyHeader($request)) {
            throw new MissingApiKey();
        }
        if (!$this->hasSignatureHeader($request)) {
            throw new InvalidSignature();
        }

        $apiKey = $this->getApiKeyFromRequest($request);

        $user = $this->userRepository->findByApiKey($apiKey);
        $this->verifySignature($request, $user->getSecretKey());
        $request->setUser($user);

        return $request;
    }

    protected function hasApiKeyHeader(Request $request)
    {
        return $request->hasHeader(self::API_KEY_HEADER);
    }

    protected function hasSignatureHeader(Request $request)
    {
        return $request->hasHeader(self::SIGNATURE_HEADER);
    }

    protected function getApiKeyFromRequest(Request $request)
    {
        return $request->getHeader(self::API_KEY_HEADER);
    }

    protected function createForbiddenResponse()
    {
        return $this->responseFactory->create(null, StatusCode::FORBIDDEN);
    }

    protected function verifySignature(Request $request, $privateKey)
    {
        $originalContent = $request->getContent();
        $calculatedSignature = hash_hmac(self::HASH_ALGORITHM, $originalContent, $privateKey);
        $signature = $request->getHeader(self::SIGNATURE_HEADER);

        if ($calculatedSignature !== $signature) {
            throw new InvalidSignature();
        }
    }
}