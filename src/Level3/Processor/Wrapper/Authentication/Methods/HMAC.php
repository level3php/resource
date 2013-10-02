<?php

namespace Level3\Security\Authentication\Methods;

use Level3\Exceptions\Forbidden;
use Level3\Messages\Request;
use Level3\Security\Authentication\CredentialsRepository;
use Level3\Security\Authentication\Credentials;

class HMAC extends Base
{
    const HASH_ALGORITHM = 'sha256';
    const TOKEN = 'Token';
    const TOKEN_SEPARATOR = ' ';
    const AUTHORIZATION_FIELDS_SEPARATOR = ':';
    private $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    protected function getCredentialsFromRequest(Request $request)
    {
        $apiKey = $this->getApiKeyFromRequest($request);
        $credentials = $this->credentialsRepository->findByApiKey($apiKey);
        return $credentials;
    }

    protected function getApiKeyFromRequest(Request $request)
    {
        $authContent = $this->extractAuthContent($request);
        $authFields = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $authContent);
        return $authFields[0];
    }

    protected function verifyCredentials(Request $request, Credentials $credentials)
    {
        $privateKey = $credentials->getSecretKey();
        return $this->verifySignature($request, $privateKey);
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

        return $calculatedSignature === $signature;
    }

    protected function extractSignatureFromRequest(Request $request)
    {
        $authContent = explode(self::AUTHORIZATION_FIELDS_SEPARATOR, $this->extractAuthContent($request));
        return $authContent[1];
    }
}
