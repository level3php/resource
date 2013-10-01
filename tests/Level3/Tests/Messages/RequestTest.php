<?php

namespace Level3\Tests;

use Level3\Messages\Request;
use Level3\Security\Authorization\Role;
use Level3\Tests\Security\Authentication\AuthenticatedCredentialsBuilder;
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

    public function testGetRepository()
    {
        $repository = $this->createRepositoryMock();
        $this->request->setRepository($repository);
        $this->assertSame($repository, $this->request->getRepository());
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
    }

    public function testGetCriteria()
    {
        $this->request->server->add(array('QUERY_STRING' => 'foo=bar'));

        $criteria = $this->request->getCriteria();
        $this->assertSame(array('foo' => 'bar'), $criteria);
    }

    public function testGetCredentials()
    {
        $credentials = $this->request->getCredentials();

        $this->assertFalse($credentials->isAuthenticated());
    }

    public function testSetAndGetCredentials()
    {
        $credentials = $this->createIrrelevantCredentials();

        $this->request->setCredentials($credentials);
        $returnedCredentials = $this->request->getCredentials();

        $this->assertThat($returnedCredentials, $this->equalTo($credentials));
    }

    public function testIsAuthenticated()
    {
        $this->assertFalse($this->request->isAuthenticated());
    }

    public function testIsAuthenticatedAfterSettingCredentials()
    {
        $credentials = $this->createIrrelevantCredentials();

        $this->request->setCredentials($credentials);

        $this->assertTrue($this->request->isAuthenticated());
    }

    private function createIrrelevantCredentials()
    {
        return AuthenticatedCredentialsBuilder::anAuthenticatedUser()
            ->withApiKey('Irrelevant API Key')
            ->withSecretKey('Ireelevant Secret Key')
            ->withLogin('someLogin')
            ->withId('anId')
            ->withFullName('Irrelevant Full Name')
            ->build();
    }

    public function testgetContent()
    {
        $content = $this->request->getContent();
        $this->assertSame(array('foo' => 'bar'), $content);
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

    public function testGetHeader()
    {
        $this->request->headers->add(array('foo'=> array('bar', 'crap')));

        $header = $this->request->getHeader('foo');
        $this->assertThat($header, $this->equalTo('bar'));
    }

    public function testGetSort()
    {
        $sort = array('foo' => 1);
        $this->request->headers->add(
            array(Request::HEADER_SORT => json_encode($sort))
        );

        $this->assertEquals($sort, $this->request->getSort());
    }

    public function testGetSortOnlyWithFieldName()
    {
        $field = 'foo';
        $this->request->headers->add(array(Request::HEADER_SORT => json_encode($field)));
        $this->assertEquals(array($field => 1), $this->request->getSort());
    }

    public function testGetSortWithAbsentHeader()
    {
        $this->assertSame(null, $this->request->getSort());
    }
}
