<?php

namespace Level3\Processor\Wrapper\Authenticator\Methods;

use Level3\Messages\Request;
use Level3\Messages\Response;
use Level3\Processor\Wrapper\Authenticator\Credentials;
use Level3\Processor\Wrapper\Authenticator\Exceptions\MalformedCredentials;
use Teapot\StatusCode;

abstract class Basic extends HeaderBased
{
    const TOKEN_SEPARATOR = ':';

    protected $scheme = 'Basic';
    protected $realm = 'My Realm';
    protected $lastVerification;

    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    protected function verifyToken(Request $request, $token)
    {
        list($user, $password) = $this->extractUserAndPassword($token);

        $this->lastVerification = $this->validateUserAndPassword($user, $password);

        return $this->lastVerification;
    }

    protected function extractUserAndPassword($token)
    {
        $parts = explode(self::TOKEN_SEPARATOR, base64_decode($token));
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

    public function modifyResponse(Response $response, $httpMethod)
    {
        if ($response->getStatusCode() != StatusCode::UNAUTHORIZED) {
            return null;
        }

        $response->setHeader(
            self::WWW_AUTHENTICATE_HEADER,
            sprintf('%s realm="%s"', $this->scheme, $this->realm)
        );
    }

    abstract protected function validateUserAndPassword($user, $password);
}
