<?php

namespace Level3\Tests\Security\Authorization;

use Level3\Security\Authorization\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped(
            'The MySQLi extension is not available.'
        );
    }

    /**
     * @dataProvider accessLevels
     */
    public function testAdminShouldHaveAccess($accessLevel)
    {
        $role = new Role();
        $role->addAdminAccess();

        $methodName = sprintf('has%sAccess', ucfirst($accessLevel));

        $this->assertTrue($role->$methodName());
    }

    /**
     * @dataProvider accessLevelsWithoutAdmin
     */
    public function testShouldOnlyHaveItsAccess($accessLevel)
    {
        $role = new Role();
        $addAccess = sprintf('add%sAccess', ucfirst($accessLevel));
        $role->$addAccess();
        $accessLevels = $this->getAccessLevels();
        $accessLevels = array_diff($accessLevels, array($accessLevel));

        foreach ($accessLevels as $notHaveLevel) {
            $hasAccess = sprintf('has%sAccess', ucfirst($notHaveLevel));
            $this->assertFalse($role->$hasAccess(), sprintf('Shouldn\'t have %s access', $notHaveLevel));
        }
        $hasAccess = sprintf('has%sAccess', ucfirst($accessLevel));
        $this->assertTrue($role->$hasAccess());
    }

    public function accessLevels()
    {
        return array(
            array('admin'),
            array('create'),
            array('delete'),
            array('list'),
            array('read'),
            array('write'),
        );
    }

    public function accessLevelsWithoutAdmin()
    {
        return array(
            array('create'),
            array('delete'),
            array('list'),
            array('read'),
            array('write'),
        );
    }

    public function getAccessLevels()
    {
        return array(
            'admin',
            'create',
            'delete',
            'list',
            'read',
            'write'
        );
    }
}