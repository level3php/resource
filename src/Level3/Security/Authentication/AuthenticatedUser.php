<?php

namespace Level3\Security\Authentication;

use Level3\Security\Authorization\Role;

class AuthenticatedUser extends User
{
    private $id;
    private $login;
    private $fullName;
    private $role;
    private $secretKey;
    private $apiKey;

    public function __construct($id, $login, $fullName, Role $role, $secretKey, $apiKey)
    {
        $this->id = $id;
        $this->login = $login;
        $this->fullName = $fullName;
        $this->role = $role;
        $this->secretKey = $secretKey;
        $this->apiKey = $apiKey;
    }

    public function isAuthenticated()
    {
        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }
}