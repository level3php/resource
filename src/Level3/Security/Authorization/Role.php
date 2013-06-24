<?php

namespace Level3\Security\Authorization;

class Role
{
    private $listAccess;
    private $readAccess;
    private $writeAccess;
    private $createAccess;
    private $deleteAccess;
    private $adminAccess;

    public function hasListAccess()
    {
        return $this->adminAccess || $this->listAccess;
    }

    public function hasReadAccess()
    {
        return $this->adminAccess || $this->readAccess;
    }

    public function hasWriteAccess()
    {
        return $this->adminAccess || $this->writeAccess;
    }

    public function hasCreateAccess()
    {
        return $this->adminAccess || $this->createAccess;
    }

    public function hasDeleteAccess()
    {
        return $this->adminAccess || $this->deleteAccess;
    }

    public function hasAdminAccess()
    {
        return $this->isAdmin();
    }

    public function addListAccess()
    {
        $this->listAccess = true;
    }

    public function addReadAccess()
    {
        $this->readAccess = true;
    }

    public function addWriteAccess()
    {
        $this->writeAccess = true;
    }

    public function addCreateAccess()
    {
        $this->createAccess = true;
    }

    public function addDeleteAccess()
    {
        $this->deleteAccess = true;
    }

    public function addAdminAccess()
    {
        $this->adminAccess = true;
    }
}