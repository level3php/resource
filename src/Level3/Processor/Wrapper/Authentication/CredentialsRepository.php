<?php

namespace Level3\Processor\Wrapper\Authentication;

interface CredentialsRepository
{
    public function findByApiKey($apiKey);
}