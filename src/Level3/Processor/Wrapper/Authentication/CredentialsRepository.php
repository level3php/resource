<?php

namespace Level3\Security\Authentication;

interface CredentialsRepository
{
    public function findByApiKey($apiKey);
}