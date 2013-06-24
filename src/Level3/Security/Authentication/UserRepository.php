<?php

namespace Level3\Security\Authentication;

interface UserRepository
{
    public function findByApiKey($apiKey);
}