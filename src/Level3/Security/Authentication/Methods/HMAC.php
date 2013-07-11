<?php

namespace Level3\Security\Authentication\Methods;

use Level3\Exceptions\Forbidden;
use Level3\Messages\Request;
use Level3\Security\Authentication\AuthenticationMethod;
use Level3\Security\Authentication\CredentialsRepository;
use Level3\Security\Authentication\Exceptions\MissingCredentials;

class HMAC implements AuthenticationMethod
{
    const HASH_ALGORITHM = 'sha256';
    const AUTHORIZATION_HEADER = 'Authorization';
    const TOKEN = 'Token';
    const TOKEN_SEPARATOR = ' ';
    const AUTHORIZATION_FIELDS_SEPARATOR = ':';
    private $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function authenticateRequest(Request $request)
    {
        if (!$this->hasAuthorizationHeader($request)) {
            throw new MissingCredentials();
        }

        $apiKey = $this->getApiKeyFromRequest($request);

        $credentials = $this->credentialsRepository->findByApiKey($apiKey);
        $this->verifySignature($request, $credentials->getSecretKey());
        $request->setCredentials($credentials);

        return $request;
    }

    protected function hasAuthorizationHeader(Request $request)
    {
        return $request->headers->has(self::AUTHORIZATION_HEADER);
    }

    protected function getApiKeyFromRequest(Request $request)
    {
        $authContent = $this->extractAuthContent($request);
        $authFields = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $authContent);
        return $authFields[0];
    }

    protected function extractAuthContent(Request $request)
    {
        $authHeader = $request->getHeader(self::AUTHORIZATION_HEADER);
        $authHeaderFirst = explode(self::TOKEN_SEPARATOR, $authHeader);

        if ($authHeaderFirst[0] !== self::TOKEN) {
            throw new Forbidden('Provided credentials are invalid');
        }

        $authHeaderSecond = explode(self::TOKEN_SEPARATOR, $authHeader);
        return $authHeaderSecond[1];
    }

    protected function verifySignature(Request $request, $privateKey)
    {
        $originalContent = $request->getContent();
        $calculatedSignature = hash_hmac(self::HASH_ALGORITHM, $originalContent, $privateKey);

        $signature = strtolower($this->extractSignatureFromRequest($request));

        if ($calculatedSignature !== $signature) {
            throw new Forbidden('Access Forbidden');
        }
    }

    protected function extractSignatureFromRequest(Request $request)
    {
        $authContent = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $this->extractAuthContent($request));
        return $authContent[1];
    }
}
