<?php

namespace Level3\Tests\Messages\Processors;

use Level3\Messages\Processors\ExceptionHandler;
use Level3\Messages\Request;
use Mockery as m;

class ExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider methods
     */
    public function testMethod($methodName)
    {
        $requestMock = m::mock('Level3\Messages\Request');
        $processorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $exceptionHandler = new ExceptionHandler($processorMock);
        $exceptionHandler->$methodName($requestMock);
    }

    public function methods()
    {
        return array(
            array('find'),
            array('get'),
            array('post'),
            array('put'),
            array('delete')
        );
    }
}