<?php

namespace Level3\Tests\Controllers;

use Level3\Controllers\Silex;
use Level3\Messages\RequestFactory;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Level3\Messages\Request as Level3Request;

class SilexTest extends \PHPUnit_Framework_TestCase
{
    const IRRELEVANT_RESPONSE = 'X';
    const IRRELEVANT_KEY = 'Y';
    const IRRELEVANT_ID = 'YY';

    private $applicationMock;
    private $processorMock;
    private $requestFactoryMock;
    private $symfonyRequest;
    private $level3Request;
    private $silex;

    public function setUp()
    {
        $this->applicationMock = m::mock('Silex\Application');
        $this->processorMock = m::mock('Level3\Messages\Processors\RequestProcessor');
        $this->requestFactoryMock = m::mock(new RequestFactory());
        $this->symfonyRequest = new SymfonyRequest(
            array(),
            array(),
            $this->createAttributes(),
            array(),
            array(),
            array(),
            null
        );
        $this->level3Request = new Level3Request(self::IRRELEVANT_KEY, $this->symfonyRequest);
        $this->silex = new Silex($this->applicationMock, $this->processorMock, $this->requestFactoryMock);
    }

    private function createAttributes()
    {
        return array(
              '_route' => sprintf('%s:asfasdf', self::IRRELEVANT_KEY)
        );
    }

    public function tearDown()
    {
        unset($applicationMock);
        unset($processorMock);
        unset($requestFactoryMock);
    }

    /**
     * @dataProvider methodNames
     */
    public function testMethod($methodName)
    {
        $this->createLevel3RequestShouldReturn($this->level3Request);
        $this->processorMock->shouldReceive($methodName)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->silex->$methodName($this->symfonyRequest);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function methodNames()
    {
        return array(
            array('find'),
            array('get'),
            array('put')
        );
    }

    /**
     * @dataProvider updatingMethodNames
     */
    public function testUpdatingMethod($methodName)
    {
        $this->createLevel3RequestShouldReturn($this->level3Request);
        $this->processorMock->shouldReceive($methodName)->once()->andReturn(self::IRRELEVANT_RESPONSE);

        $response = $this->silex->$methodName($this->symfonyRequest, self::IRRELEVANT_ID);

        $this->assertThat($response, $this->equalTo(self::IRRELEVANT_RESPONSE));
    }

    public function updatingMethodNames()
    {
        return array(
            array('post'),
            array('delete')
        );
    }

    private function createLevel3RequestShouldReturn($level3request)
    {
        $this->requestFactoryMock->shouldReceive('create')->withNoArgs()->once()->andReturn($level3request);
    }
}