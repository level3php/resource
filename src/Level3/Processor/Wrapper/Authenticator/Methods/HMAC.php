<?php

namespace Level3\Processor\Wrapper\Authenticator\Methods;

use Level3\Messages\Request;
use Level3\Processor\Wrapper\Authenticator\Credentials;
use Level3\Processor\Wrapper\Authenticator\Exceptions\MalformedCredentials;
use Exception;

abstract class HMAC extends HeaderBased
{
    const TOKEN_SEPARATOR = ':';
    protected $scheme = 'HMAC';
    protected $hashAlgorithm = 'sha256';
    protected $lastVerification;

    public function setHashAlgorithm($algorithm)
    {
        if (!in_array($algorithm, hash_algos())) {
            throw new Exception(sprintf(
                'The given algorithm "%s" not is supported',
                $algorithm
            ));
        }

        $this->hashAlgorithm = $algorithm;
    }

    protected function verifyToken(Request $request, $token)
    {
        list($apiKey, $signature) = $this->extractApiKeyAndSignature($token);

        $privateKey = $this->getPrivateKey($apiKey);
        $content = $request->getRawContent();

        $calculatedSignature = hash_hmac($this->hashAlgorithm, $content, $privateKey);

        if (strcasecmp($calculatedSignature, $signature) == 0) {
            $this->lastVerification = true;
        } else {
            $this->lastVerification = false;
        }

        return $this->lastVerification;
    }

    protected function extractApiKeyAndSignature($token)
    {
        $parts = explode(self::TOKEN_SEPARATOR, $token);
        if (count($parts) != 2) {
            throw new MalformedCredentials();
        }

        return $parts;
    }

    protected function modifyRequest(Request $request, $httpMethod)
    {
        $credentials = new Credentials($this->lastVerification);
        $request->setCredentials($credentials);

        $this->lastVerification = null;
    }

    abstract protected function getPrivateKey($apiKey);
}
