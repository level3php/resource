<?php

namespace Level3\Security\Authentication\Methods;

use Level3\Security\Authentication\AuthenticationMethod;
use Level3\Exceptions\Forbidden;
use Level3\Messages\Request;
use Level3\Security\Authentication\Credentials;
use Level3\Security\Authentication\Exceptions\MissingCredentials;

abstract class Base implements AuthenticationMethod
{
    const AUTHORIZATION_HEADER = 'Authorization';

    public final function authenticateRequest(Request $request)
    {
        if (!$this->hasAuthorizationHeader($request)) {
            throw new MissingCredentials();
        }

        $credentials = $this->getAndVerifyCredentials($request);
        $request->setCredentials($credentials);
        return $request;
    }

    protected function hasAuthorizationHeader(Request $request)
    {
        return $request->headers->has(static::AUTHORIZATION_HEADER);
    }

    private final function getAndVerifyCredentials(Request $request)
    {
        $credentials = $this->getCredentialsFromRequest($request);
        if (!$this->verifyCredentials($request, $credentials)) {
            throw new Forbidden('Provided credentials are invalid');
        }
        return $credentials;
    }

    protected abstract function getCredentialsFromRequest(Request $request);

    protected abstract function verifyCredentials(Request $request, Credentials $credentials);
}
