<?php

namespace Level3\Processor\Wrapper\Authorization;

class Role
{
    private $listAccess = false;
    private $readAccess = false;
    private $writeAccess = false;
    private $createAccess = false;
    private $deleteAccess = false;
    private $adminAccess = false;

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
        return $this->adminAccess;
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