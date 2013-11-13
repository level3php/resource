<?php

namespace Level3\Tests;

use Level3\Messages\Request;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestTest extends TestCase
{
    const IRRELEVANT_KEY = 'X';
    const IRRELEVANT_ID = 'XX';
    const IRRELEVANT_CONTENT = '{"foo":"bar"}';

    private $dummySymfonyRequest;
    private $request;

    public function setUp()
    {
        $this->dummySymfonyRequest = new SymfonyRequest(array(), array(), array(), array(), array(), array(), self::IRRELEVANT_CONTENT);
        $this->request = new Request(self::IRRELEVANT_KEY, $this->dummySymfonyRequest);
    }

    public function tearDown()
    {
        unset($this->dummySymfonyRequest);
    }

    public function testGetFormatter()
    {
        $formatter = $this->request->getFormatter();
        $this->assertInstanceOf('Level3\Formatter', $formatter);
    }

    public function testGetKey()
    {
        $key = $this->request->getKey();
        $this->assertThat($key, $this->equalTo(self::IRRELEVANT_KEY));
    }

    public function testGetParameters()
    {
        $attributes = $this->request->getAttributes();
        $this->assertInstanceOf('Level3\Resource\Parameters', $attributes);
    }

    public function testGetFilters()
    {
        $filters = $this->request->GetFilters();
        $this->assertInstanceOf('Level3\Resource\Parameters', $filters);

        $data = $filters->all();
        $this->assertTrue(array_key_exists('range', $data));
        $this->assertTrue(array_key_exists('sort', $data));
        $this->assertTrue(array_key_exists('criteria', $data));
        $this->assertTrue(array_key_exists('expand', $data));
    }

    public function testGetCriteria()
    {
        $this->request->server->add(array('QUERY_STRING' => 'foo=bar'));

        $criteria = $this->request->getCriteria();
        $this->assertSame(array('foo' => 'bar'), $criteria);
    }

    public function testGetCredentials()
    {
        $credentials = m::mock('Level3\Processor\Wrapper\Authenticator\Credentials');

        $this->request->setCredentials($credentials);
        $this->assertSame($credentials, $this->request->getCredentials());
    }

    public function testIsAuthenticated()
    {
        $credentials = m::mock('Level3\Processor\Wrapper\Authenticator\Credentials');
        $credentials->shouldReceive('isAuthenticated')->once()->andReturn(true);

        $this->request->setCredentials($credentials);
        $this->assertTrue($this->request->isAuthenticated());
    }

    public function testIsAuthenticatedNonCredentials()
    {
        $this->assertNull($this->request->isAuthenticated());
    }

    public function testgetContent()
    {
        $content = $this->request->getContent();
        $this->assertSame(array('foo' => 'bar'), $content);
    }

    public function testgetRawContent()
    {
        $content = $this->request->getRawContent();
        $this->assertSame('{"foo":"bar"}', $content);
    }

    public function testGetRange()
    {
        $this->request->headers->add(array('Range' => 'entity=0-9'));

        $range = $this->request->getRange();
        $this->assertThat($range, $this->equalTo(array(0,9)));
    }

    public function testGetRangeWithoutLowerBound()
    {
        $this->request->headers->add(array('Range' => 'entity=-9'));

        $range = $this->request->getRange();
        $this->assertThat($range, $this->equalTo(array(0,9)));
    }

    public function testGetRangeWithoutUpperBound()
    {
        $this->request->headers->add(array('Range' => 'entity=9-'));

        $range = $this->request->getRange();
        $this->assertThat($range, $this->equalTo(array(9,0)));
    }

    public function testGetRangeWithoutHeader()
    {
        $range = $this->request->getRange();
        $this->assertThat($range, $this->equalTo(array(0,0)));
    }


    public function testGetExpand()
    {
        $this->request->headers->add(array('X-Expand-Links' => 'foo;qux.bar'));

        $expand = $this->request->getExpand();
        $this->assertThat($expand, $this->equalTo(array(
            array('foo'),
            array('qux','bar')
        )));
    }

    public function testGetExpandWithoutHeader()
    {
        $expand = $this->request->getExpand();
        $this->assertThat($expand, $this->equalTo(array()));
    }

    public function testGetHeader()
    {
        $this->request->headers->add(array('foo'=> array('bar', 'crap')));

        $header = $this->request->getHeader('foo');
        $this->assertThat($header, $this->equalTo('bar'));
    }

    public function testGetSort()
    {
        $this->request->headers->add(
            array(Request::HEADER_SORT => ' foo = 1; bar;baz  =-1')
        );

        $this->assertEquals(
            array('foo' => 1, 'bar' => 1, 'baz' => -1),
            $this->request->getSort());
    }

    public function testGetSortEmpty()
    {
        $this->request->headers->add(
            array(Request::HEADER_SORT => '')
        );

        $this->assertEquals(
            array(),
            $this->request->getSort());
    }

    public function testGetSortWithAbsentHeader()
    {
        $this->assertSame(null, $this->request->getSort());
    }
}
