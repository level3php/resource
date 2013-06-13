<?php
namespace Level3\Tests;
use Level3\ResourceHub;
use Level3\Mocks\Mapper;
use Level3\Mocks\ResourceManager;

use Teapot\StatusCode;

class TestCase extends \PHPUnit_Framework_TestCase
{   
    protected function getHub()
    {
        $mapper = new Mapper;

        $hub = new ResourceHub();
        $hub->setMapper($mapper);

        return $hub;
    }
}
