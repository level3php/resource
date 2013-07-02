<?php

namespace Level3\Security\Authentication\Methods;

use Level3\Messages\Request;
use Level3\Security\Authentication\UserRepository;
use Level3\Security\Authentication\Exceptions\InvalidCredentials;
use Level3\Security\Authentication\Exceptions\MissingCredentials;
use Level3\Security\Authentication\Exceptions\BadCredentials;
use Level3\Security\Authentication\AuthenticationMethod;

class HMAC implements AuthenticationMethod
{
    const HASH_ALGORITHM = 'sha256';
    const AUTHORIZATION_HEADER = 'Authorization';
    const TOKEN = 'Token';
    const TOKEN_SEPARATOR = ' ';
    const AUTHORIZATION_FIELDS_SEPARATOR = ':';

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticateRequest(Request $request)
    {
        if (!$this->hasAuthorizationHeader($request)) {
            throw new MissingCredentials();
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

    protected function getApiKeyFromRequest(Request $request)
    {
        $authContent = $this->extractAuthContent($request);
        $authFields = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $authContent);
        return $authFields[0];
    }

    protected function verifySignature(Request $request, $privateKey)
    {
        $originalContent = $request->getContent();
        $calculatedSignature = hash_hmac(self::HASH_ALGORITHM, $originalContent, $privateKey);

        $signature = $this->extractSignatureFromRequest($request);

        if ($calculatedSignature !== $signature) {
            throw new BadCredentials();
        }
    }

    protected function extractSignatureFromRequest(Request $request)
    {
        $authContent = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $this->extractAuthContent($request));
        return $authContent[1];
    }

    protected function extractAuthContent(Request $request)
    {
        $authHeader = $request->getHeader(self::AUTHORIZATION_HEADER);
        $authHeaderFirst = explode(self::TOKEN_SEPARATOR, $authHeader);

        if ($authHeaderFirst[0] !== self::TOKEN) {
            throw new InvalidCredentials();
        }

        $authHeaderSecond = explode(self::TOKEN_SEPARATOR, $authHeader);
        return $authHeaderSecond[1];
    }
}
