<?php

namespace Level3\Processor\Wrapper\Authenticator\Methods;

use Level3\Processor\Wrapper\Authenticator\Method;
use Level3\Messages\Request;
use Level3\Messages\Response;
use Level3\Processor\Wrapper\Authenticator\Exceptions\Unauthorized;
use Level3\Processor\Wrapper\Authenticator\Exceptions\MissingCredentials;
use Level3\Processor\Wrapper\Authenticator\Exceptions\MalformedCredentials;
use Level3\Processor\Wrapper\Authenticator\Exceptions\InvalidScheme;
use Teapot\StatusCode;

abstract class HeaderBased implements Method
{
    const AUTHORIZATION_HEADER = 'Authorization';
    const WWW_AUTHENTICATE_HEADER = 'WWW-Authenticate';

    protected $scheme;
    protected $continueWithoutAuthentication = false;

    public function continueWithoutAuthentication($continue)
    {
        $this->continueWithoutAuthentication = $continue;
    }

    public function authenticateRequest(Request $request, $httpMethod)
    {
        if (!$this->hasAuthorizationHeader($request)) {
            if (!$this->continueWithoutAuthentication) {
                throw new MissingCredentials();
            }

            return;
        }

        $this->verifyAuthorizationHeader($request);
        $this->modifyRequest($request, $httpMethod);

    }

    protected function hasAuthorizationHeader(Request $request)
    {
        return $request->getHeader(static::AUTHORIZATION_HEADER) !== null;
    }

    private function verifyAuthorizationHeader(Request $request)
    {
        list($scheme, $token) = $this->getCredentialFromAuthorizationHeader($request);

        if (!$this->verifyScheme($request, $scheme)) {
            throw new InvalidScheme();
        }

        if (!$this->verifyToken($request, $token)) {
            throw new Unauthorized('Provided credentials are invalid');
        }
    }

    protected function getCredentialFromAuthorizationHeader(Request $request)
    {
        $header = $request->getHeader(static::AUTHORIZATION_HEADER);

        preg_match_all('/(?P<scheme>[a-z]+) (?P<token>.*)$/i', $header, $data);

        if (count($data['scheme']) == 0 || count($data['token']) == 0) {
            throw new MalformedCredentials();
        }

        return [$data['scheme'][0], $data['token'][0]];
    }

    protected function verifyScheme(Request $request, $scheme)
    {
        return strtolower($this->scheme) == strtolower($scheme);
    }

    public function modifyResponse(Response $response, $httpMethod)
    {
        if ($response->getStatusCode() != StatusCode::UNAUTHORIZED) {
            return null;
        }

        $response->setHeader(self::WWW_AUTHENTICATE_HEADER, $this->scheme);
    }

    abstract protected function verifyToken(Request $request, $token);
    abstract protected function modifyRequest(Request $request, $httpMethod);
}
